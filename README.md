# Credit index counter / kreditindex számoló

## Summary in English

This is a credit index counter for ELTE University (Hungary) students.

I'm planning to do an English update where I translate the website.

Programming language: PHP

Language: Hungarian

## Leírás

Kreditindex számoló ELTÉ-seknek a [2017-es HKR alapján](https://www.elte.hu/dstore/document/898/ELTE_SZMSZ_II_170530.pdf). (Azért írtam kifejezetten, hogy ELTÉ-seknek, mert nem tudom, hogy más egyetemeken is pontosan így számolnak-e. Amennyiben igen, akkor megköszönöm a visszajelzést.)

Programozási nyelv: PHP

## Mit számol?

- Kreditindex
- Korrigált kreditindex
- Teljesített kredit
- Hagyományos átlag
- Súlyozott átlag

| Megnevezés | Számolás menete |
| - | - |
| Kreditindex | $\text{KI} \quad = \quad \dfrac{\sum (\text{teljesített kredit} \quad \cdot \quad \text{érdemjegy})}{30}$ |
| Korrigált kreditindex | $\text{KKI} \quad = \quad \dfrac{\sum (\text{teljesített kredit} \quad \cdot \quad \text{érdemjegy})}{30} \cdot \dfrac{\text{teljesített kredit}}{\text{felvett kredit}}$ |
| Teljesített kredit | $\sum \text{teljesített kredit}$ |
| Hagyományos átlag | $\text{HA} \quad = \quad \dfrac{\sum \text{érdemjegy}}{\sum \text{tárgyak száma}}$ |
| Súlyozott átlag | $\text{TÁ} \quad = \quad \dfrac{\sum (\text{teljesített kredit} \quad \cdot \quad \text{érdemjegy})}{\sum \text{teljesített kredit}}$ |
