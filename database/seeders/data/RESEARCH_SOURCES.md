# Research Sources & Notes — Venezuela 2026 Earthquake Response Seed Data

**Compiled:** 2026-06-28
**Scope:** Caracas (Distrito Capital), La Guaira (Vargas), and Miranda.

## Event summary (verified)

Two major earthquakes struck Venezuela on **24 June 2026** at ~18:04 local time,
**39 seconds apart**: the first **Mw 7.2**, the second **Mw 7.5**. Epicenters were near
**San Felipe / Yumare, Yaracuy** (NW of Caracas). Depths ~20 km and ~10 km. USGS estimated
Mercalli intensity up to IX.

**La Guaira (Vargas)** was the worst-affected state: news reported **>100 buildings collapsed**
and ~**250 buildings damaged**, with **Caraballeda** and **Catia La Mar** the hardest-hit sectors.
In Caracas, building collapses were concentrated in **San Bernardino** (Libertador) and
**Los Palos Grandes / Altamira** (Chacao). Casualty figures rose over several days and varied by
source (235 → 920 → 1,430 deaths reported across 24–26 June); these figures are volatile and were
NOT seeded as fixed data.

This was preceded by a **September 2025** seismic doublet (Mw 6.2 / 6.3) affecting Zulia and Lara —
mentioned for context but outside the Caracas/La Guaira/Miranda scope of this seed.

---

## DATASET A — Hospitals & health centers (8 entries)

These facilities exist independently of the earthquake and were sourced from medical directories,
Wikipedia, and official MPPS/IVSS references. Coordinates are **approximate** (OpenStreetMap /
Wikipedia / map listings) and should be field-verified before use in routing.

| Hospital | Municipality / State | Confidence | Source |
|---|---|---|---|
| Hospital Universitario de Caracas (HUC) | Libertador / Distrito Capital | high | es.wikipedia.org/wiki/Hospital_Universitario_de_Caracas |
| Hospital Vargas de Caracas | Libertador / Distrito Capital | high | es.wikipedia.org/wiki/Hospital_José_María_Vargas |
| Hospital de Niños J. M. de los Ríos | Libertador / Distrito Capital | high | es.wikipedia.org/wiki/Hospital_de_Niños_J._M._de_los_Ríos |
| Hospital Dr. Domingo Luciani (El Llanito) | Sucre / Miranda | high | cnnespanol.cnn.com (24 Jun 2026) + Moovit |
| Hospital Pérez Carreño / Gral. del Oeste | Libertador / Distrito Capital | medium | sociedadvenezolanacirugiaplastica.org |
| Maternidad Concepción Palacios | Libertador / Distrito Capital | high | es.wikipedia.org/wiki/Maternidad_Concepción_Palacios |
| Clínica El Ávila | Chacao / Miranda | medium | cnnespanol.cnn.com (24 Jun 2026) |
| Salud Chacao (red municipal) | Chacao / Miranda | medium | cnnespanol.cnn.com (24 Jun 2026) |

**Note on quake impact to hospitals:** News reported **8 hospitals affected** in the disaster zone,
some evacuated, with field hospitals set up for overflow. Sources did **not** name which specific
hospitals were damaged, so no hospital is seeded with a damaged/collapsed status. Hospital Domingo
Luciani, Clínica El Ávila and Salud Chacao are confirmed to have *received earthquake survivors*
(i.e. operating). HUC sits inside Ciudad Universitaria (reported affected) and J. M. de los Ríos
sits in San Bernardino (where residential buildings collapsed) — but the hospitals' own structural
status was not separately confirmed.

---

## DATASET B — Earthquake-affected buildings & sites (11 entries)

| Entry | Sector / Municipality / State | Status | Confidence |
|---|---|---|---|
| Edificio Petunia | Los Palos Grandes / Chacao / Miranda | colapsado | medium |
| Edificio Rita | San Bernardino / Libertador / D.C. | dañado | medium |
| Edificio Marama (5 pisos) | San Bernardino / Libertador / D.C. | colapsado | medium |
| Edificio Moisés | San Bernardino / Libertador / D.C. | desplome parcial | medium |
| Edificio 22 plantas (sin nombre) | Altamira / Chacao / Miranda | colapsado | low |
| Hotel Eduard's | (zona n/c) / La Guaira | totalmente destruido | medium |
| Estadio Jorge Luis García Carneiro (refugio) | La Guaira | habilitado como refugio | medium |
| Aeropuerto Int. de Maiquetía | Maiquetía / Vargas / La Guaira | dañado, inoperativo | high |
| Sector Caraballeda | Vargas / La Guaira | daños graves | high |
| Sector Catia La Mar / Playa Grande | Vargas / La Guaira | daños graves | high |

### Source list (Dataset B)
- **Wikipedia — Terremotos de Venezuela de 2026:** https://es.wikipedia.org/wiki/Terremotos_de_Venezuela_de_2026 (edificios Rita/Marama/Moisés in San Bernardino; Hotel Eduard's; event details)
- **El Tiempo (Colombia), 25 Jun 2026:** https://www.eltiempo.com/mundo/venezuela/ultimas-noticias-del-fuerte-terremoto-en-venezuela-este-jueves-25-de-junio-reacciones-del-mundo-balance-de-danos-cifras-de-heridos-y-muertos-3566923 (>100 collapsed in La Guaira; Caraballeda/Catia La Mar; airport; Marama 5-story collapse)
- **El Colombiano, 25 Jun 2026:** https://www.elcolombiano.com/internacional/la-guaira-revive-tragedia-historica-terremoto-venezuela-destruccion-victimas-LN38170586 (Estadio García Carneiro as shelter; airport damage)
- **CNN en Español, 24 Jun 2026:** https://cnnespanol.cnn.com/2026/06/24/venezuela/terremoto-caracas-venezuela-trax (Edificio Petunia in Los Palos Grandes; mayor Gustavo Duque; 23 rescued in Chacao; hospitals receiving them)
- **Univisión, 26 Jun 2026:** https://www.univision.com/noticias/america-latina/sismos-venezuela-ultimas-noticias-terremotos-actualizaciones-hoy-viernes-26-junio-2026 (Caraballeda/Catia La Mar; schools as shelters/donation centers; aid distribution)
- **RPP, 24 Jun 2026:** https://rpp.pe/mundo/venezuela/terremoto-en-venezuela-2026-sismo-de-75-causa-danos-y-colapso-de-edificios-en-caracas-noticia-1694552 (22-story building in Altamira — name unconfirmed)
- **Efecto Cocuyo, 24 Jun 2026:** https://efectococuyo.com/la-humanidad/reportan-edificios-caidos-en-los-palos-grandes-y-altamira-y-san-bernardino-en-caracas-por-el-terremoto/ (corroborates the three affected sectors; explicitly notes "no hay reporte oficial" of named buildings yet)

---

## GAPS & WHAT COULD NOT BE VERIFIED (read this)

1. **No street-level addresses or coordinates for collapsed buildings.** Named buildings
   (Petunia, Rita, Marama, Moisés, Hotel Eduard's) have a sector but no confirmed street address or
   lat/lng. These must be geolocated on the ground before being used for navigation/dispatch.

2. **Per-building status is uneven.** For the San Bernardino trio, only Marama (full collapse) and
   Moisés (partial collapse) have explicit per-building status; "Rita" is grouped as affected.

3. **The Altamira 22-story building is LOW confidence** — reported by one journalist, no name, no
   exact address. Flagged so it is not mistaken for a confirmed, located structure.

4. **La Guaira individual buildings are essentially undocumented in named form.** The worst-hit
   state has >100 collapses but, as of 25–26 June, sources reported them only in aggregate and by
   sector (Caraballeda, Catia La Mar). Individual residential building names were NOT in the public
   record. Caraballeda and Catia La Mar are seeded as **sector-level** entries, not buildings.

5. **Which 8 hospitals were damaged/evacuated is not named** in available sources. No hospital is
   seeded as damaged. Treat hospital operational status as needing confirmation post-quake.

6. **Shelters/centros de acopio are thinly documented.** Only the Estadio Jorge Luis García Carneiro
   is named; news says "schools" generically were used as shelters and donation centers without
   listing them. No individual school could be verified by name.

7. **Casualty/missing figures were deliberately NOT seeded** — they changed daily and varied widely
   by outlet (235 → 920 → 1,430 deaths; up to 50,000+ reported unaccounted). Pull these live rather
   than freezing them in the database.

8. **All hospital coordinates are approximate.** Sourced from map services / encyclopedic entries,
   not surveyed. Verify before use in distance-based routing.
