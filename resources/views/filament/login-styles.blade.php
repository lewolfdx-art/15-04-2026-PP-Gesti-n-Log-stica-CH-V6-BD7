<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');

    body {
        font-family: 'Montserrat', system-ui, sans-serif;
        background: linear-gradient(rgba(0,0,0,0.88), rgba(0,0,0,0.88)),
                    url('/images/fondo-concreteras.jpg') center/cover no-repeat !important;
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    /* Contenedor del login */
    .fi-login-card {
        background: rgba(20, 20, 40, 0.45) !important;
        backdrop-filter: blur(20px) !important;
        border-radius: 30px !important;
        border: 1px solid rgba(255, 102, 0, 0.3) !important;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7) !important;
        width: 420px !important;
        padding: 55px 45px !important;
    }

    .fi-login-heading {
        color: #ff6600 !important;
        font-size: 34px !important;
        font-weight: 700 !important;
        text-shadow: 0 0 25px rgba(255, 102, 0, 0.7);
    }

    .fi-label {
        color: #ff6600 !important;
        font-weight: 600 !important;
    }

    .fi-input {
        background: rgba(255,255,255,0.15) !important;
        border: none !important;
        border-radius: 15px !important;
        color: #fff !important;
        padding: 16px 20px !important;
    }

    .fi-input:focus {
        background: rgba(255,255,255,0.25) !important;
        box-shadow: 0 0 20px rgba(255, 102, 0, 0.5) !important;
    }

    .fi-btn-primary {
        background: linear-gradient(45deg, #ff6600, #ff3300) !important;
        border-radius: 50px !important;
        padding: 16px !important;
        font-weight: 700 !important;
        box-shadow: 0 10px 25px rgba(255, 102, 0, 0.5) !important;
    }

    .fi-btn-primary:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 15px 35px rgba(255, 102, 0, 0.7) !important;
    }
</style>