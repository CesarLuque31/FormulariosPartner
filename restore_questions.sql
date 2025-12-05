-- Restaurar preguntas eliminadas del Paso 10 y 11

-- P10 - Validación Venta
INSERT INTO raz_preguntas_auditorias (formulario_id, seccion, orden, texto, tipo_campo, opciones, requerido, created_at, updated_at)
VALUES 
(1, 'P10 - Validación Venta', 57, '¿Se generó la orden?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P10 - Validación No Aplica', 62, '¿Se generó la orden?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE());

-- P11 - Análisis Proceso
INSERT INTO raz_preguntas_auditorias (formulario_id, seccion, orden, texto, tipo_campo, opciones, requerido, created_at, updated_at)
VALUES 
(1, 'P11 - Análisis Proceso', 77, '¿Se generó la orden?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Proceso', 78, '¿Se instaló el servicio?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Proceso', 79, '¿Se entregó el chip y el equipo?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Proceso', 80, '¿Se entregó el equipo?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Proceso', 81, 'Porque no se concreto la venta ?', 'textarea', NULL, 1, GETDATE(), GETDATE());

-- P11 - Análisis Agente
INSERT INTO raz_preguntas_auditorias (formulario_id, seccion, orden, texto, tipo_campo, opciones, requerido, created_at, updated_at)
VALUES 
(1, 'P11 - Análisis Agente', 83, '¿Se generó la orden?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Agente', 84, '¿Se instaló el servicio?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Agente', 85, '¿Se entregó el chip y el equipo?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Agente', 86, '¿Se entregó el equipo?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Agente', 87, 'Porque no se concreto la venta ?', 'textarea', NULL, 1, GETDATE(), GETDATE());

-- P11 - Análisis Cliente
INSERT INTO raz_preguntas_auditorias (formulario_id, seccion, orden, texto, tipo_campo, opciones, requerido, created_at, updated_at)
VALUES 
(1, 'P11 - Análisis Cliente', 89, '¿Se generó la orden?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Cliente', 90, '¿Se instaló el servicio?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Cliente', 91, '¿Se entregó el chip y el equipo?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Cliente', 92, '¿Se entregó el equipo?', 'radio', '[{"value":"SI","label":"SI"},{"value":"NO","label":"NO"},{"value":"NO APLICA","label":"NO APLICA"}]', 1, GETDATE(), GETDATE()),
(1, 'P11 - Análisis Cliente', 93, 'Porque no se concreto la venta ?', 'textarea', NULL, 1, GETDATE(), GETDATE());
