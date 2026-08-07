from pathlib import Path

from PIL import Image


SOURCE = Path(r"C:/Users/ayoub/AppData/Local/Temp/codex-clipboard-91b45c0b-079d-4f19-aa20-0aa934d143c2.png")
TARGET = Path("tmp/pdfs/phone_icon.png")


def main() -> None:
    img = Image.open(SOURCE).convert("RGBA")
    pixels = img.load()
    width, height = img.size

    min_x, min_y = width, height
    max_x, max_y = 0, 0

    for y in range(height):
        for x in range(width):
            r, g, b, a = pixels[x, y]
            if a > 0 and r < 40 and g < 40 and b < 40:
                min_x = min(min_x, x)
                min_y = min(min_y, y)
                max_x = max(max_x, x)
                max_y = max(max_y, y)

    cropped = img.crop((min_x, min_y, max_x + 1, max_y + 1))
    out = Image.new("RGBA", cropped.size, (255, 255, 255, 0))
    src = cropped.load()
    dst = out.load()

    for y in range(cropped.size[1]):
        for x in range(cropped.size[0]):
            r, g, b, a = src[x, y]
            if a > 0 and r < 60 and g < 60 and b < 60:
                dst[x, y] = (0, 0, 0, 255)
            else:
                dst[x, y] = (255, 255, 255, 0)

    TARGET.parent.mkdir(parents=True, exist_ok=True)
    out.save(TARGET)
    print(TARGET.resolve())


if __name__ == "__main__":
    main()
