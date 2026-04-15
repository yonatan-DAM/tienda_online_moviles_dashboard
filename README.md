# 📱 TechStore Admin - Sistema de Gestión E-commerce

Este es un sistema integral de gestión para una tienda online de tecnología, desarrollado con un enfoque en diseño **Dark Mode Premium** y una experiencia de usuario fluida. El panel permite administrar inventario, visualizar ventas en tiempo real y analizar métricas clave.

## ✨ Características Principales

### 🖥️ Dashboard Administrativo
- **KPI Cards:** Visualización rápida de productos activos, valor total del inventario y número de pedidos.
- **Gráficos Dinámicos:** Gráfica de barras (Chart.js) que muestra la distribución de stock por modelo.
- **Feed de Ventas:** Tabla de últimos pedidos vinculada a la base de datos de clientes.
- **Interfaz Premium:** Diseño moderno basado en paleta de colores slate/blue con efectos de "Glassmorphism".
- **Avatar 3D Dinámico:** Icono de usuario con animación personalizada de "giro de trompo" (rotateY) mediante CSS3.

### 📦 Gestión de Inventario
- **CRUD Completo:** Listado, edición y eliminación de productos.
- **Borrado Lógico:** Sistema de seguridad que desactiva productos (`activo = 0`) en lugar de borrarlos físicamente, manteniendo la integridad de las facturas históricas.
- **Manejo de Imágenes:** Almacenamiento de imágenes de productos directamente en la base de datos (BLOB).

### 🛒 Sistema de Pedidos
- **Historial de Ventas:** Visualización detallada de todos los pedidos realizados.
- **Detalle de Pedido:** Vista específica que muestra el cliente, la fecha y el desglose de productos comprados con sus respectivas miniaturas.

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 8.x
- **Base de Datos:** MySQL (MariaDB)
- **Frontend:** HTML5, CSS3 (Custom Properties & Keyframes)
- **Librerías:** - [Chart.js](https://www.chartjs.org/) para analítica visual.
  - [FontAwesome 6](https://fontawesome.com/) para iconografía.
  - Google Fonts (Inter/Segoe UI).

## 🚀 Instalación y Configuración

1. **Requisitos:** Tener instalado XAMPP, WAMP o un servidor con soporte PHP y MySQL.
2. **Base de Datos:**
   - Crear una base de datos llamada `tienda3`.
   - Importar el archivo `database.sql` (incluido en la carpeta raíz).
3. **Configuración de Conexión:**
   - Editar el archivo `conexion.php` con tus credenciales locales (usuario, contraseña y host).
4. **Acceso:**
   - Colocar la carpeta en `htdocs`.
   - Acceder vía `http://localhost/tu-carpeta/login.php`.
   - **Credenciales por defecto:** Usuario: `admin` | Password: `admin`.

## 📂 Estructura del Proyecto

```text
├── admin/
│   ├── img/                # Recursos visuales 
│   ├── panel_admin.php     # Dashboard principal
│   ├── ver_productos.php   # Gestión de stock
│   ├── ver_pedidos.php     # Listado de ventas
│   └── detalle_pedido.php  # Desglose de factura
├── conexion.php            # Configuración de BD
├── proteger.php            # Sistema de seguridad de sesiones
├── login.php               # Acceso con diseño profesional
└── index.php               # Vista pública de la tienda
