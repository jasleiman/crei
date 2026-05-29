#!/usr/bin/env python3
"""Genera import_obras_sociales.csv e import_alumnos*.csv desde maestrobd.csv."""
import csv
import hashlib
import re
import unicodedata
from pathlib import Path

BASE = Path(__file__).parent
SQL = BASE / "crei_integracion.sql"
MAESTRO = BASE / "maestrobd.csv"


def norm(s):
    s = (s or "").strip().upper()
    s = unicodedata.normalize("NFD", s)
    s = "".join(c for c in s if unicodedata.category(c) != "Mn")
    return re.sub(r"\s+", " ", s)


def load_existing_obras():
    existing = {}
    in_obras = False
    for line in open(SQL, encoding="utf-8", errors="ignore"):
        if "INSERT INTO `obras_sociales`" in line:
            in_obras = True
            continue
        if in_obras:
            if line.startswith("--") or "CREATE TABLE" in line:
                break
            for m in re.finditer(r"\(\d+, '([^']*)'", line):
                desc = m.group(1).strip()
                if desc:
                    existing[norm(desc)] = desc
    return existing


def load_alumnos_db():
    names = {}
    in_alu = False
    for line in open(SQL, encoding="utf-8", errors="ignore"):
        if "INSERT INTO `alumnos`" in line:
            in_alu = True
            continue
        if in_alu:
            if "INSERT INTO `" in line and "alumnos" not in line:
                break
            for m in re.finditer(r"\(\d+, '([^']*)',", line):
                n = m.group(1).strip()
                names[norm(n)] = n
    return names


MAESTRA_EMAIL = {
    norm("ANTONELA PICCO"): "piccoantonela@hotmail.com",
    norm("CECILIA CONCA"): "cecilia_conca@hotmail.com",
    norm("CINTIA FRANCO"): "cintiafranco@live.com.ar",
    norm("CLAUDIA GONZALEZ"): "clauvg1606@gmail.com",
    norm("CLAUDIA GONZÁLEZ"): "clauvg1606@gmail.com",
    norm("DANIELA ATILIO"): "daniela_atilio@yahoo.com.ar",
    norm("FERNANDA ONTIVERO"): "fer.detez@gmail.com",
    norm("FERNANDA ONTIVEROS"): "fer.detez@gmail.com",
    norm("GABRIELA CARASI"): "gabyemi1@hotmail.com",
    norm("LEONELA BAEZ"): "leonelabaez429@gmail.com",
    norm("LUCIA TEDESCO"): "lucia.s.tedesco@gmail.com",
    norm("LUCÍA TEDESCO"): "lucia.s.tedesco@gmail.com",
    norm("LUCIANA HIDALGO"): "lucianagonzalezhidalgo@gmail.com",
    norm("MARIA PAZ CERESOLI"): "ceresolimariapaz@gmail.com",
    norm("MA. PAZ CERESOLI"): "ceresolimariapaz@gmail.com",
    norm("MARIA LUSNEQUE"): "mariar_91@hotmail.com",
    norm("MARIANA PUIGDÉNGOLAS"): "marianapuigdengolas@hotmail.com",
    norm("MARIANA PUIGDEONGOLAS"): "marianapuigdengolas@hotmail.com",
    norm("MARIANA PUIGDENDOLAS"): "marianapuigdengolas@hotmail.com",
    norm("MARIELA TOVAGLIARI"): "mariela.tovagliari@crei.edu.ar",
    norm("MICAELA COLUCCI"): "micaela.mcolussi@gmail.com",
    norm("MIKALIS GOMEZ FUENTES"): "prof.psicop@gmail.com",
    norm("MIKALIS GOMEZ"): "prof.psicop@gmail.com",
    norm("NATALIA ACRI"): "acrinatalia@hotmail.com",
    norm("NOELIA VILLALBA"): "noeliavillalba171118@gmail.com",
    norm("VALERIA VILLALBA"): "noeliavillalba171118@gmail.com",
    norm("PAULA QUIROGA"): "paulaquiroga10@hotmail.com",
    norm("ROMINA KONRADI"): "rominakonradi@hotmail.com.ar",
    norm("ROMINA RUSSO"): "russoromina@yahoo.com.ar",
    norm("SABRINA DOMINGUEZ"): "sabdominguez1@gmail.com",
    norm("SABRINA DOMÍNGUEZ"): "sabdominguez1@gmail.com",
    norm("SANDRA VALLEJOS"): "caro.sandra@hotmail.com",
    norm("SILVIA ACOSTA"): "acostasilvia-32@hotmail.com",
    norm("SOFIA CUOMO"): "ms.cuomo23@gmail.com",
    norm("SOFÍA CUOMO"): "ms.cuomo23@gmail.com",
    norm("VANINA CIRIGLIANO"): "vaninaciri09@gmail.com",
    norm("YADIA MERCE"): "yadiamerce@gmail.com",
    norm("YADIA MERCÉ"): "yadiamerce@gmail.com",
    norm("YANET NUÑEZ"): "psicopeyanetnunez@gmail.com",
}

OBRA_RAW_MAP = {
    norm("UNION PERSONAL"): "UNION PERSONAL",
    norm("UNIÓN PERSONAL"): "UNION PERSONAL",
    norm("OSDE 210"): "OSDE 210",
    norm("OSDEPYM"): "OSDEPYM",
    norm("OSPDEPYM"): "OSPDEPYM ",
    norm("OSPIP"): "OSPIP",
    norm("OSDE"): "OSDE",
    norm("OSECAC"): "OSECAC",
    norm("OSECAC "): "OSECAC ",
    norm("CORPORACIÓN ASISTENCIAL"): "CORPORACIÓN ASISTENCIAL",
    norm("CORPORACION MÉDICA"): "CORPORACION MÉDICA",
    norm("OSCHOCA"): "OSCHOCA",
    norm("OSUTHGRA"): "OSUTHGRA",
    norm("OSPOCE"): "OSPOCE",
    norm("OSPP"): "OSPP ",
    norm("GALENO AZUL"): "GALENO",
    norm("GALENO"): "GALENO",
    norm("BECA"): "BECA ",
    norm("ASI"): "ASI CLASSIC ",
    norm("ASI CLASSIC"): "ASI CLASSIC ",
    norm("ASI SALUD"): "ASI SALUD",
    norm("HOSPITAL ITALIANO"): "HOSPITAL ITALIANO ",
    norm("MEDICUS"): "MEDICUS",
    norm("OSUMSA MEDICUS"): "OSUMSA MEDICUS",
    norm("MEDIFE"): "MEDIFE",
    norm("OMINT"): "OMINT",
    norm("OSPIA"): "OSPIA",
    norm("OSPIT"): "OSPIT",
    norm("OSPACA"): "OSPACA",
    norm("OSPECOM"): "OSPECOM",
    norm("OSPICHA"): "OSPICHA",
    norm("OSSEG"): "OSSEG",
    norm("OSPSA"): "OSPSA",
    norm("PAMI"): "PAMI",
    norm("PREMEDIC"): "PREMEDIC",
    norm("SANCOR SALUD"): "SANCOR SALUD",
    norm("SWISS MEDICAL"): "SWISS MEDICAL",
    norm("SERVICIO PENITENCIARIO"): "SERVICIO PENITENCIARIO",
    norm("PODER JUDICIAL"): "PODER JUDICIAL",
    norm("MAESTRANZA"): "MAESTRANZA",
    norm("UOM"): "UOM",
    norm("WILLIAM HOPE"): "WILLIAM HOPE",
    norm("LUIS PASTEUR"): "LUIS PASTEUR",
    norm("OSMMEDT"): "OSMMEDT",
    norm("OSTPBA"): "OSTPBA",
    norm("OSUOMRA"): "OSUOMRA",
    norm("OSMED"): "OSMED",
    norm("OSFE/GALENO"): "OSFE/GALENO",
    norm("ASE"): "ASE",
    norm("ALMAFUERTE"): "PARTICULAR",
}

COORD_EMAIL = {
    "ERNA": "ernastieben@gmail.com",
    "MARTINA": "martina.pedrat@escuela.crei.edu.ar",
    "CHANTAL": "chantal.montero@escuela.crei.edu.ar",
    "ANDREA": "Lic.andreaccabrera@gmail.com",
    "CLAUDIA": "claudia.villalba@escuela.crei.edu.ar",
    "MARISA": "marisaradaelli77@gmail.com",
}

ALUMNOS_HEADERS = [
    "nombre", "dni", "servicio", "email", "telefono", "direccion", "localidad",
    "partido", "codigo_postal", "provincia", "padre", "diagnostico",
    "obra_social", "maestra_integradora", "coordinador",
    "fecha_inicio", "fecha_fin", "escuela", "orientacion", "acta_acuerdo",
]

MAESTRAS_HEADERS = [
    "nombre", "email", "clave", "perfil", "tipo_profesional", "coordinador", "coordinador2",
]


def split_coords(txt):
    out = []
    for part in (txt or "").replace("|", ";").split(";"):
        p = part.strip()
        if p:
            out.append(p)
    return out


def merge_coord_columns(existing_parts, extra_parts):
    """Une coordinadores sin duplicar (case-insensitive)."""
    merged = []
    seen = set()
    for c in existing_parts + extra_parts:
        key = c.lower()
        if key not in seen:
            seen.add(key)
            merged.append(c)
    if len(merged) <= 2:
        return "; ".join(merged), ""
    return "; ".join(merged[:2]), "; ".join(merged[2:])


def sync_maestras_coordinadores_from_alumnos(alumnos, maestras_path):
    """
    Ajusta import_maestras_coordinadores.csv para que cada maestra tenga
    todos los coordinadores que aparecen en import_alumnos.csv.
    """
    mi_coords = {}
    for row in alumnos:
        mi = (row.get("maestra_integradora") or "").strip()
        coord = (row.get("coordinador") or "").strip()
        if not mi or not coord:
            continue
        mi_coords.setdefault(mi.lower(), set()).add(coord)

    if not maestras_path.exists():
        return 0

    rows = []
    with open(maestras_path, newline="", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        fieldnames = reader.fieldnames or MAESTRAS_HEADERS
        for row in reader:
            email = (row.get("email") or "").strip()
            nombre = (row.get("nombre") or "").strip()
            from_alumnos = set()
            for key in (email.lower(), nombre.upper()):
                if key:
                    from_alumnos |= mi_coords.get(key, set())
            existing = split_coords(row.get("coordinador")) + split_coords(row.get("coordinador2"))
            coord1, coord2 = merge_coord_columns(existing, sorted(from_alumnos))
            row["coordinador"] = coord1
            row["coordinador2"] = coord2
            rows.append(row)

    with open(maestras_path, "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=fieldnames)
        w.writeheader()
        w.writerows(rows)

    return len(rows)


def find_obra(raw, existing_obras=None):
    """Normaliza nombre de obra social según planilla y catálogo conocido."""
    existing_obras = existing_obras or {}
    raw = (raw or "").strip()
    for sep in [" cambia", " CAMBIA"]:
        if sep.lower() in raw.lower():
            raw = re.split(sep, raw, flags=re.I)[0].strip()
    n = norm(raw)
    if not n:
        return "PARTICULAR"
    if n in OBRA_RAW_MAP:
        mapped = OBRA_RAW_MAP[n]
        key = norm(mapped)
        if key in existing_obras:
            return existing_obras[key]
        return mapped
    if n in existing_obras:
        return existing_obras[n]
    for k, v in existing_obras.items():
        if n in k or k in n:
            return v
    return raw.strip()


def resolve_coord(asistente):
    a = (asistente or "").upper()
    for key in ["ERNA", "MARTINA", "CHANTAL", "ANDREA", "CLAUDIA", "MARISA"]:
        if key in a:
            return COORD_EMAIL[key]
    return "ernastieben@gmail.com"


def resolve_maestra(docente):
    n = norm(docente)
    if n in MAESTRA_EMAIL and MAESTRA_EMAIL[n]:
        return MAESTRA_EMAIL[n]
    return docente.strip()


def norm_servicio(nivel):
    n = norm(nivel).replace("INCIAL", "INICIAL")
    if "INICIAL" in n:
        return "INICIAL"
    if "SECUNDARIO" in n:
        return "SECUNDARIO"
    return "PRIMARIO"


def pseudo_dni(nombre, seen):
    base = "9" + str(int(hashlib.md5(nombre.encode()).hexdigest()[:7], 16) % 9000000).zfill(7)
    dni = base
    while dni in seen:
        dni = str(int(dni) + 1)
    seen.add(dni)
    return dni


def main():
    existing_obras = load_existing_obras()

    rows = []
    with open(MAESTRO, encoding="utf-8-sig") as f:
        reader = csv.reader(f, delimiter=";")
        next(reader)
        for r in reader:
            while len(r) < 10:
                r.append("")
            rows.append(r)

    obras_used = {}
    for r in rows:
        obras_used[find_obra(r[1], existing_obras)] = True

    obras_list = sorted(obras_used.keys())

    with open(BASE / "import_obras_sociales.csv", "w", newline="", encoding="utf-8") as f:
        w = csv.writer(f)
        w.writerow([
            "descripcion", "telefono", "contacto", "direccion", "localidad",
            "partido", "codigo_postal", "provincia", "email",
        ])
        for o in obras_list:
            w.writerow([o, "", "", "", "", "", "", "", ""])

    seen_dni = set()
    alumnos = []
    for r in rows:
        nombre = r[0].strip()
        if not nombre:
            continue
        obs = r[9].strip() if len(r) > 9 else ""
        orientacion = r[6].strip()
        if obs:
            orientacion = (orientacion + " | " + obs).strip(" |")
        alumnos.append({
            "nombre": nombre,
            "dni": pseudo_dni(nombre, seen_dni),
            "servicio": norm_servicio(r[5]),
            "email": "",
            "telefono": "",
            "direccion": r[3].strip(),
            "localidad": "",
            "partido": "",
            "codigo_postal": "",
            "provincia": "",
            "padre": "",
            "diagnostico": "",
            "obra_social": find_obra(r[1], existing_obras),
            "maestra_integradora": resolve_maestra(r[7]),
            "coordinador": resolve_coord(r[8]),
            "fecha_inicio": "01/03/2026",
            "fecha_fin": "20/12/2026",
            "escuela": r[2].strip(),
            "orientacion": orientacion,
            "acta_acuerdo": "",
        })

    with open(BASE / "import_alumnos.csv", "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=ALUMNOS_HEADERS)
        w.writeheader()
        w.writerows(alumnos)

    # import_alumnos_planilla_completa.csv = mismo contenido (alias)
    with open(BASE / "import_alumnos_planilla_completa.csv", "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=ALUMNOS_HEADERS)
        w.writeheader()
        w.writerows(alumnos)

    maestras_path = BASE / "import_maestras_coordinadores.csv"
    n_maestras = sync_maestras_coordinadores_from_alumnos(alumnos, maestras_path)

    print(f"Obras sociales: {len(obras_list)} -> import_obras_sociales.csv")
    for o in obras_list:
        print(f"  · {o}")
    print(f"Alumnos: {len(alumnos)} -> import_alumnos.csv")
    if n_maestras:
        print(f"Maestras: coordinadores sincronizados con alumnos ({n_maestras} filas) -> import_maestras_coordinadores.csv")


if __name__ == "__main__":
    main()
