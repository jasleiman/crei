-- Perfil 4 (Apoyo + Maestra): quitar menú «Cargar horas equipo» (id_menus 43).
-- El menú «Cargar horas» (id_menus 1) debe seguir asignado.
DELETE FROM perfiles_menus WHERE id_perfiles = 4 AND id_menus = 43;
