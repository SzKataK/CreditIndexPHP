# Credit Index Calculator

<a href="https://www.php.net/" target="_blank"><img style="margin: 10px" src="https://profilinator.rishav.dev/skills-assets/php-original.svg" alt="PHP" height="50" /></a>
<a href="https://www.javascript.com/" target="_blank"><img style="margin: 10px" src="https://profilinator.rishav.dev/skills-assets/javascript-original.svg" alt="JavaScript" height="50" /></a>
<a href="https://www.w3schools.com/css/" target="_blank"><img style="margin: 10px" src="https://profilinator.rishav.dev/skills-assets/css3-original-wordmark.svg" alt="CSS3" height="50" /></a>

## EN - Credit Index Calculator - English Overview

### About the Project

This is an online calculator designed for Hungarian university students to easily calculate their credit index and other academic performance metrics.

### Features

- Supports multiple Hungarian universities (list available on the website)
- Available in Hungarian and English
- Responsive and clean UI
- Paste the course data from Neptun directly for instant results

### Formulas Used

|Metric | Formula |
|--------|---------|
| Credit Index | $\dfrac{\sum (\text{completed credit} \cdot \text{grade})}{30}$ |
| Corrected Credit Index | $\dfrac{\sum (\text{completed credit} \cdot \text{grade})}{30} \cdot \dfrac{\sum \text{completed credit}}{\sum \text{registered credit}}$ |
| Completed Credits | $\sum \text{completed credit}$ |
| Traditional Average | $\dfrac{\sum \text{grade}}{\sum \text{subjects}}$ |
| Weighted Average | $\dfrac{\sum (\text{completed credit} \cdot \text{grade})}{\sum \text{completed credit}}$ |

### Usage

[![Live Site](https://img.shields.io/badge/Live%20Site-credit.katiesz.live-blue?style=flat-square)](https://credit.katiesz.live)

No installation required.

### Planned Features

These are currently in development or on the roadmap:

- Neptun Excel import
- Dark mode

### License

This project is freely usable under the terms of the MIT license.

### Acknowledgements

Special thanks to [Valentinusz](https://valentinusz.github.io/notes/webprog/storage) for allowing the use of the `storage.php` library.

## HU - Kreditindex Számoló - Magyar leírás

### A projektről

Magyar egyetemi hallgatók számára készült online kalkulátor. Könnyen kiszámolható vele a kreditindex és a többi gyakran használt tanulmányi mutató.

### Főbb funkciók

- Több magyar egyetemet is támogat (a pontos lista az oldalon található)
- Magyar és angol nyelven is elérhető
- Letisztult, reszponzív felhasználói felület
- Csak másold be a Neptunból a tantárgylistát, és az eredmények azonnal megjelennek

### Használt képletek

| Megnevezés | Számolás menete |
| - | - |
| Kreditindex | $\text{KI}  =  \dfrac{\sum (\text{teljesített kredit}  \cdot  \text{érdemjegy})}{30}$ |
| Korrigált kreditindex | $\text{KKI}  =  \dfrac{\sum (\text{teljesített kredit}  \cdot  \text{érdemjegy})}{30} \cdot \dfrac{\sum \text{teljesített kredit}}{\sum \text{felvett kredit}}$ |
| Teljesített kredit | $\sum \text{teljesített kredit}$ |
| Hagyományos átlag | $\text{HA}  =  \dfrac{\sum \text{érdemjegy}}{\sum \text{tárgyak száma}}$ |
| Súlyozott átlag | $\text{TÁ}  =  \dfrac{\sum (\text{teljesített kredit}  \cdot  \text{érdemjegy})}{\sum \text{teljesített kredit}}$ |

### Használat

[![Live Site](https://img.shields.io/badge/Live%20Site-credit.katiesz.live-blue?style=flat-square)](https://credit.katiesz.live)

Nem igényel telepítést.

### Tervezett funkciók

Az alábbi fejlesztéseket tervezem:

- Neptun Excel fájl importálása
- Dark mode (sötét téma)

### Licenc

A projekt szabadon felhasználható az MIT licenc feltételei szerint.

### Köszönetnyilvánítás

Külön köszönet [Valentinusznak](https://valentinusz.github.io/notes/webprog/storage), hogy engedélyezte a `storage.php` könyvtár használatát.
