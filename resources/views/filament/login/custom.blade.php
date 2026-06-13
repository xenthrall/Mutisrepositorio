<style>
    /* ================================
       1. Layout y Fondo Principal
    ================================= */

    /* MODO CLARO: Fondo completamente blanco */
    body,
    .fi-simple-layout {
        background-color: #ffffff !important;
        transition: background-color 0.3s ease;
    }

    /* MODO OSCURO: Fondo oscuro (Zinc 950) */
    .dark body,
    .dark .fi-simple-layout {
        background-color: #09090b !important;
    }

    /* Ocultar cualquier pseudo-elemento de fondo */
    .fi-simple-layout::before {
        display: none !important;
    }

    /* ================================
       2. Tarjeta Principal (Plana)
    ================================= */

    .fi-simple-main {
        background-color: transparent !important;
        box-shadow: none !important;
        border: none !important;
        max-width: 28rem !important;
        margin: 0 auto;
        padding-top: 2rem !important;
    }

    .fi-simple-header .fi-logo {
        height: 3rem !important;
        margin-bottom: 0.5rem;
    }

    /* ================================
       3. Títulos y Textos
    ================================= */

    /* Título principal */
    .fi-simple-header h1 {
        color: #000000 !important;
        font-weight: 600 !important;
        font-size: 1.5rem !important;
        letter-spacing: normal !important;
        text-transform: none !important;
    }

    .dark .fi-simple-header h1 {
        color: #ffffff !important;
    }

    /* Subtítulo */
    .fi-simple-header p {
        color: #6b7280 !important;
        /* gray-500 */
    }

    .dark .fi-simple-header p {
        color: #a1a1aa !important;
        /* zinc-400 */
    }

    /* Labels de los inputs */
    .fi-fo-field-wrp-label span {
        font-weight: 600 !important;
        color: #111827 !important;
    }

    .dark .fi-fo-field-wrp-label span {
        color: #e4e4e7 !important;
        /* zinc-200 */
    }

    /* ================================
       4. Inputs (Cajas de texto)
    ================================= */

    /* MODO CLARO: Fondo azul claro */
    .fi-input-wrapper {
        background-color: #eff6ff !important;
        /* blue-50 */
        border: 1px solid transparent !important;
        box-shadow: none !important;
        border-radius: 0.5rem !important;
        transition: border-color 0.2s;
    }

    .fi-input {
        background-color: transparent !important;
        color: #111827 !important;
    }

    .fi-input-wrapper:focus-within {
        border-color: #cbd5e1 !important;
        /* slate-300 */
    }

    /* MODO OSCURO: Fondo oscuro sutil con borde */
    .dark .fi-input-wrapper {
        background-color: #18181b !important;
        /* zinc-900 */
        border: 1px solid #27272a !important;
        /* zinc-800 */
    }

    .dark .fi-input {
        color: #ffffff !important;
    }

    .dark .fi-input-wrapper:focus-within {
        border-color: #52525b !important;
        /* zinc-600 */
    }

    /* ================================
       5. Botón Principal (Entrar)
    ================================= */

    /* MODO CLARO: Botón negro texto blanco */
    .fi-btn-color-primary {
        background-color: #18181b !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: none !important;
        border-radius: 0.5rem !important;
        font-weight: 500 !important;
        transition: background-color 0.2s;
    }

    .fi-btn-color-primary:hover {
        background-color: #27272a !important;
    }

    /* MODO OSCURO: Botón blanco texto negro (estilo minimalista) */
    .dark .fi-btn-color-primary {
        background-color: #ffffff !important;
        color: #18181b !important;
    }

    .dark .fi-btn-color-primary:hover {
        background-color: #e4e4e7 !important;
        /* zinc-200 */
    }

    .btn-volver-inicio:hover {
        color: #111827;
        /* gray-900 */
    }

    /* MODO OSCURO: Enlace volver al inicio */
    .dark .btn-volver-inicio {
        color: #a1a1aa;
        /* zinc-400 */
    }

    .dark .btn-volver-inicio:hover {
        color: #ffffff;
    }

    .btn-volver-inicio svg {
        width: 1.25rem;
        height: 1.25rem;
    }
</style>