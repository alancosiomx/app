-- cierres_servicio
CREATE TABLE IF NOT EXISTS cierres_servicio (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket VARCHAR(50) NOT NULL,
  atiende VARCHAR(150) NOT NULL,
  resultado ENUM('Éxito','Rechazo') NOT NULL,
  serie_instalada VARCHAR(100) NULL,
  serie_retirada VARCHAR(100) NULL,
  solucion VARCHAR(255) NULL,
  solucion_especifica VARCHAR(255) NULL,
  motivo_rechazo VARCHAR(255) NULL,
  observaciones TEXT NULL,
  cerrado_por VARCHAR(100) NOT NULL,
  fecha_cierre DATETIME NOT NULL,
  latitud DECIMAL(10,6) NULL,
  longitud DECIMAL(10,6) NULL,
  foto_fachada VARCHAR(255) NULL,
  foto_hs VARCHAR(255) NULL,
  foto_serie_inst VARCHAR(255) NULL,
  foto_serie_ret VARCHAR(255) NULL,
  foto_rechazo VARCHAR(255) NULL,
  UNIQUE KEY u_ticket (ticket)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- (opcional) log de acciones durante el llenado, por si quieres auditar pasos
CREATE TABLE IF NOT EXISTS log_cierre_interactivo (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket VARCHAR(50) NOT NULL,
  idc VARCHAR(100) NOT NULL,
  evento VARCHAR(100) NOT NULL,
  valor TEXT NULL,
  ts DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
