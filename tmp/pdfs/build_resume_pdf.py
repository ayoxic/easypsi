from pathlib import Path
import sys

from PIL import Image
from reportlab.lib.utils import ImageReader
from reportlab.pdfgen import canvas


def flatten_image(image: Image.Image) -> Image.Image:
    if image.mode in {"RGBA", "LA"} or (
        image.mode == "P" and "transparency" in image.info
    ):
        base = Image.new("RGB", image.size, "white")
        alpha_source = image.convert("RGBA")
        base.paste(alpha_source, mask=alpha_source.getchannel("A"))
        return base
    return image.convert("RGB")


def build_pdf(image_paths: list[Path], output_path: Path) -> None:
    output_path.parent.mkdir(parents=True, exist_ok=True)

    pdf = canvas.Canvas(str(output_path))
    pdf.setTitle("Ayoub Janina Resume")

    for image_path in image_paths:
        image = flatten_image(Image.open(image_path))
        width, height = image.size
        pdf.setPageSize((width, height))
        pdf.drawImage(ImageReader(image), 0, 0, width=width, height=height)
        pdf.showPage()

    pdf.save()


def main() -> int:
    if len(sys.argv) < 4:
        print(
            "Usage: build_resume_pdf.py <output-pdf> <image-1> <image-2> [image-n ...]",
            file=sys.stderr,
        )
        return 1

    output_path = Path(sys.argv[1]).resolve()
    image_paths = [Path(arg).resolve() for arg in sys.argv[2:]]
    build_pdf(image_paths, output_path)
    print(output_path)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
