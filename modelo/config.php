<?php
// modelo/config.php
require_once 'conexion.php';

function obtenerDatosUsuario($conn, $id_usuario) {
    try {
        $query = "SELECT nombre, correo, foto_perfil FROM usuarios WHERE id = :id_usuario";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            return [
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo'],
                'foto_perfil' => $usuario['foto_perfil'],
                'password' => '********' // No mostrar contraseña real
            ];
        }
        
        return null;
    } catch (PDOException $e) {
        error_log("Error al obtener datos del usuario: " . $e->getMessage());
        return null;
    }
}

function actualizarPerfil($conn, $id_usuario, $nombre, $correo) {
    try {
        $query = "UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE id = :id_usuario";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error al actualizar perfil: " . $e->getMessage());
        return false;
    }
}

// Función para verificar contraseña actual
function verificarPasswordActual($conn, $id_usuario, $password_actual) {
    try {
        $query = "SELECT password FROM usuarios WHERE id = :id_usuario";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($password_actual, $usuario['password'])) {
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Error al verificar contraseña actual: " . $e->getMessage());
        return false;
    }
}

function cambiarPassword($conn, $id_usuario, $password_actual, $nueva_password) {
    try {
        // 1. Verificar contraseña actual
        if (!verificarPasswordActual($conn, $id_usuario, $password_actual)) {
            return ['success' => false, 'message' => 'La contraseña actual es incorrecta'];
        }
        
        // 2. Validar que no sea la misma contraseña
        if ($password_actual === $nueva_password) {
            return ['success' => false, 'message' => 'La nueva contraseña no puede ser igual a la actual'];
        }
        
        // 3. Validar que la contraseña no esté vacía
        if (empty(trim($nueva_password))) {
            return ['success' => false, 'message' => 'La contraseña no puede estar vacía'];
        }
        
        // 4. Validar longitud mínima
        if (strlen($nueva_password) < 6) {
            return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'];
        }
        
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        
        $query = "UPDATE usuarios SET password = :password WHERE id = :id_usuario";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Verificar si realmente se actualizó alguna fila
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se pudo actualizar la contraseña. Usuario no encontrado.'];
            }
        } else {
            return ['success' => false, 'message' => 'Error al ejecutar la consulta'];
        }
        
    } catch (PDOException $e) {
        error_log("Error al cambiar contraseña: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error de base de datos al cambiar contraseña'];
    }
}

function subirFotoPerfil($conn, $id_usuario, $file) {
    // Validar que sea una imagen
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Solo se permiten archivos JPEG, PNG, GIF y WebP'];
    }
    
    // Validar tamaño (máximo 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'message' => 'La imagen no debe superar los 2MB'];
    }
    
    // Crear directorio si no existe
    if (!file_exists('fotos')) {
        mkdir('fotos', 0777, true);
    }
    
    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nuevo_nombre = 'perfil_' . $id_usuario . '_' . time() . '.' . $extension;
    $ruta_destino = 'fotos/' . $nuevo_nombre;
    
    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {
        try {
            // Obtener foto anterior antes de actualizar
            $query_anterior = "SELECT foto_perfil FROM usuarios WHERE id = :id_usuario";
            $stmt_anterior = $conn->prepare($query_anterior);
            $stmt_anterior->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt_anterior->execute();
            $foto_anterior_data = $stmt_anterior->fetch(PDO::FETCH_ASSOC);
            $foto_anterior_nombre = $foto_anterior_data['foto_perfil'] ?? null;
            
            // Actualizar con la nueva foto
            $query = "UPDATE usuarios SET foto_perfil = :foto_perfil WHERE id = :id_usuario";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':foto_perfil', $nuevo_nombre);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Eliminar foto anterior si existe y no es la default
                if ($foto_anterior_nombre && $foto_anterior_nombre != $nuevo_nombre) {
                    $ruta_foto_anterior = 'fotos/' . $foto_anterior_nombre;
                    if (file_exists($ruta_foto_anterior) && !str_contains($foto_anterior_nombre, 'default')) {
                        unlink($ruta_foto_anterior);
                    }
                }
                
                // Actualizar sesión
                $_SESSION['foto_perfil'] = $nuevo_nombre;
                
                return [
                    'success' => true, 
                    'message' => 'Foto de perfil actualizada correctamente', 
                    'ruta' => $ruta_destino,
                    'nombre_archivo' => $nuevo_nombre
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar foto en BD: " . $e->getMessage());
            // Eliminar archivo subido si falla la BD
            if (file_exists($ruta_destino)) {
                unlink($ruta_destino);
            }
        }
    }
    
    return ['success' => false, 'message' => 'Error al subir la imagen'];
}

// Obtener datos del usuario actual
if (isset($_SESSION['id_usuario']) && isset($conn)) {
    $datosUsuario = obtenerDatosUsuario($conn, $_SESSION['id_usuario']);
}
?>