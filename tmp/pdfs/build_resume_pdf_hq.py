from pathlib import Path
import sys

from PIL import Image, ImageDraw, ImageFilter, ImageFont
from reportlab.lib.utils import ImageReader
from reportlab.pdfgen import canvas


UPSCALE_FACTOR = 4


def flatten_image(image: Image.Image) -> Image.Image:
    if image.mode in {"RGBA", "LA"} or (
        image.mode == "P" and "transparency" in image.info
    ):
        base = Image.new("RGB", image.size, "white")
        alpha_source = image.convert("RGBA")
        base.paste(alpha_source, mask=alpha_source.getchannel("A"))
        return base
    return image.convert("RGB")


def upscale_image(image: Image.Image, factor: int = UPSCALE_FACTOR) -> Image.Image:
    width, height = image.size
    upscaled = image.resize(
        (width * factor, height * factor),
        resample=Image.Resampling.LANCZOS,
    )
    return upscaled.filter(ImageFilter.UnsharpMask(radius=1.4, percent=150, threshold=2))


def patch_projects_header(page: Image.Image, factor: int = UPSCALE_FACTOR) -> Image.Image:
    page = page.copy()
    draw = ImageDraw.Draw(page)

    x1, y1, x2, y2 = [value * factor for value in (210, 10, 577, 38)]
    draw.rectangle((x1, y1, x2, y2), fill="white")

    line_y = 24 * factor
    draw.line((214 * factor, line_y, 575 * factor, line_y), fill=(185, 185, 185), width=max(1, factor))

    font_path = Path("C:/Windows/Fonts/arial.ttf")
    font = ImageFont.truetype(str(font_path), 8 * factor)
    draw.text((218 * factor, 11 * factor), "PROJECTS", fill=(78, 78, 78), font=font)
    return page


def build_pdf(output_path: Path, page_specs: list[tuple[Path, Image.Image]]) -> None:
    output_path.parent.mkdir(parents=True, exist_ok=True)
    pdf = canvas.Canvas(str(output_path))
    pdf.setTitle("Ayoub Janina Resume")

    for original_path, processed_image in page_specs:
        original_width, original_height = Image.open(original_path).size
        pdf.setPageSize((original_width, original_height))
        pdf.drawImage(
            ImageReader(processed_image),
            0,
            0,
            width=original_width,
            height=original_height,
        )
        pdf.showPage()

    pdf.save()


def main() -> int:
    if len(sys.argv) != 4:
        print(
            "Usage: build_resume_pdf_hq.py <output-pdf> <page-1-image> <page-2-image>",
            file=sys.stderr,
        )
        return 1

    output_path = Path(sys.argv[1]).resolve()
    page1_path = Path(sys.argv[2]).resolve()
    page2_path = Path(sys.argv[3]).resolve()

    page1 = upscale_image(flatten_image(Image.open(page1_path)))
    page2 = flatten_image(Image.open(page2_path))
    page2 = patch_projects_header(upscale_image(page2))

    build_pdf(output_path, [(page1_path, page1), (page2_path, page2)])
    print(output_path)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
