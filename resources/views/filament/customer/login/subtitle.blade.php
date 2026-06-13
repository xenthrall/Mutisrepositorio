<style>
    /* ================================
       1. Layout y Fondo Principal
    ================================= */

    body,
    .fi-simple-layout {
        background-color: #ffffff !important;
        transition: background-color 0.3s ease;
    }

    .dark body,
    .dark .fi-simple-layout {
        background-color: #09090b !important;
    }

    .fi-simple-layout::before {
        display: none !important;
    }

    /* ================================
       2. Tarjeta Principal
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
       3. Textos
    ================================= */

    .fi-simple-header h1 {
        color: #000000 !important;
        font-weight: 600 !important;
        font-size: 1.5rem !important;
    }

    .dark .fi-simple-header h1 {
        color: #ffffff !important;
    }

    /* Subtítulo */
    .login-subtitle {
        font-size: 0.9rem;
        font-weight: 500;
        color: #6b7280;
        text-align: center;
        margin-bottom: 1rem;
    }

    .dark .login-subtitle {
        color: #a1a1aa;
    }

    /* ================================
       4. Inputs
    ================================= */

    .fi-input-wrapper {
        background-color: #eff6ff !important;
        border: 1px solid transparent !important;
        border-radius: 0.5rem !important;
    }

    .fi-input {
        background-color: transparent !important;
        color: #111827 !important;
    }

    .fi-input-wrapper:focus-within {
        border-color: #cbd5e1 !important;
    }

    .dark .fi-input-wrapper {
        background-color: #18181b !important;
        border: 1px solid #27272a !important;
    }

    .dark .fi-input {
        color: #ffffff !important;
    }

    /* ================================
       5. Botón login
    ================================= */

    .fi-btn-color-primary {
        background-color: #18181b !important;
        color: #ffffff !important;
        border-radius: 0.5rem !important;
    }

    .fi-btn-color-primary:hover {
        background-color: #27272a !important;
    }

    .dark .fi-btn-color-primary {
        background-color: #ffffff !important;
        color: #18181b !important;
    }

    /* ================================
       6. Botón volver
    ================================= */

    .btn-volver-inicio {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 1rem;
        text-decoration: none;
        transition: color 0.2s;
    }

    .btn-volver-inicio:hover {
        color: #111827;
    }

    .dark .btn-volver-inicio {
        color: #a1a1aa;
    }

    .dark .btn-volver-inicio:hover {
        color: #ffffff;
    }

    .btn-volver-inicio svg {
        width: 1rem;
        height: 1rem;
    }
    .login-back {
    text-align: center;
    margin-top: 0.25rem;
    margin-bottom: 0.5rem;
}

.login-back a {
    font-size: 0.8rem;
    color: #9ca3af; /* gray-400 */
    text-decoration: none;
    transition: color 0.2s;
}

.login-back a:hover {
    color: #6b7280; /* gray-500 */
}

.dark .login-back a {
    color: #71717a; /* zinc-500 */
}

.dark .login-back a:hover {
    color: #a1a1aa; /* zinc-400 */
}
.fi-simple-header h1 {
    display: none !important;
}

.fi-simple-header .fi-logo {
    height: 3.5rem !important;   /* más grande */
    margin-bottom: 0.75rem;
}

@media (min-width: 768px) {
    .fi-simple-header .fi-logo {
        height: 3.5rem !important;
    }
}
</style>


<div class="login-subtitle">
    Centro de garantías LED
</div>