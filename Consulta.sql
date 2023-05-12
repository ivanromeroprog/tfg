SELECT

s.id_presentacion_actividad,
s.titulo,
s.fecha, AVG(porcentaje) AS promedio_porcentaje,
s.id_curso

FROM 

(
SELECT

alumno.id AS id_alumno,
alumno.nombre,
alumno.apellido,
curso.id AS id_curso,
presentacion_actividad.id AS id_presentacion_actividad,
presentacion_actividad.titulo,
presentacion_actividad.fecha, SUM(interaccion.correcto) AS correctos, COUNT(interaccion.id) AS cantidad, SUM(interaccion.correcto) / COUNT(interaccion.id) * 100 AS porcentaje
FROM presentacion_actividad
LEFT JOIN detalle_presentacion_actividad ON presentacion_actividad.id = detalle_presentacion_actividad.presentacion_actividad_id
LEFT JOIN interaccion ON interaccion.detalle_presentacion_actividad_id = detalle_presentacion_actividad.id
LEFT JOIN alumno ON alumno.id = interaccion.alumno_id
LEFT JOIN curso ON presentacion_actividad.curso_id = curso.id
WHERE presentacion_actividad.curso_id = 1 AND presentacion_actividad.estado <> "Anulado" AND (detalle_presentacion_actividad.tipo = "Pregunta" OR detalle_presentacion_actividad.tipo = "Concepto A")
GROUP BY presentacion_actividad.id, alumno.id
ORDER BY presentacion_actividad.fecha ASC, alumno.apellido ASC) AS s
GROUP BY s.id_presentacion_actividad
ORDER BY fecha ASC;



