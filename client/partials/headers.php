<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frozen Foods - Dashboard</title>
    <link rel="shortcut icon" href="/frozen_foods/assets/img/favicon.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- LUcide icons -->
    <script src="https://unpkg.com/lucide@latest/dist/lucide.umd.js"></script>

    <!-- Lottie JSON -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.6/lottie.min.js"></script>

    <!-- Icons stylesheets -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom stylesheets -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            font-family: 'DM Sans';
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        dm: ['DM Sans', 'sans-serif']
                    },
                    colors: {
                        accent: '#F97316',
                        dark: '#201f20',
                        secondary: '#ff7272',
                        gray: '#f6f7fc',
                        'custom-gray': '#f6f7fc',
                        'custom-dark': '#201f20'
                    },
                    boxShadow: {
                        soft: '0 2px 15px rgba(0, 0, 0, 0.08)',
                        medium: '0 4px 25px rgba(0, 0, 0, 0.1)',
                        large: '0 8px 35px rgba(0, 0, 0, 0.12)',
                        accent: '0 4px 20px rgba(249, 115, 22, 0.15)',
                        glow: '0 0 30px rgba(249, 115, 22, 0.3)'
                    },
                    animation: {
                        'fade-in': 'fade-in 0.4s ease-out',
                        'slide-up': 'slide-up 0.3s ease-out',
                        'slide-down': 'slide-down 0.3s ease-out',
                        'slide-right': 'slideRight 0.4s ease-out',
                        'slide-left': 'slideLeft 0.4s ease-out',
                        'scale-in': 'scale-in 0.2s ease-out',
                        'bounce-gentle': 'bounce-gentle 0.6s ease-in-out',
                        'pulse-ring': 'pulseRing 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'wiggle': 'wiggle 1s ease-in-out infinite',
                        'shimmer': 'shimmer 2s linear infinite'
                    },
                    keyframes: {
                        'fadeIn': {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        'slideUp': {
                            '0%': {
                                transform: 'translateY(20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        },
                        'slideDown': {
                            '0%': {
                                transform: 'translateY(-20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        },
                        'slideRight': {
                            '0%': {
                                transform: 'translateX(-100%)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateX(0)',
                                opacity: '1'
                            }
                        },
                        'slideLeft': {
                            '0%': {
                                transform: 'translateX(100%)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateX(0)',
                                opacity: '1'
                            }
                        },
                        'scaleIn': {
                            '0%': {
                                transform: 'scale(0.9)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'scale(1)',
                                opacity: '1'
                            }
                        },
                        'bounceGentle': {
                            '0%, 20%, 50%, 80%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '40%': {
                                transform: 'translateY(-10px)'
                            },
                            '60%': {
                                transform: 'translateY(-5px)'
                            }
                        },
                        'pulseRing': {
                            '0%': {
                                transform: 'scale(.33)'
                            },
                            '80%, 100%': {
                                transform: 'scale(2.33)',
                                opacity: '0'
                            }
                        },
                        'float': {
                            '0%, 100%': {
                                transform: 'translateY(0px)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            }
                        },
                        'wiggle': {
                            '0%, 100%': {
                                transform: 'rotate(-3deg)'
                            },
                            '50%': {
                                transform: 'rotate(3deg)'
                            }
                        },
                        'shimmer': {
                            '0%': {
                                backgroundPosition: '-200% 0'
                            },
                            '100%': {
                                backgroundPosition: '200% 0'
                            }
                        },
                        'bounce-gentle': {
                            '0%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-4px)'
                            }
                        },
                        'scale-in': {
                            '0%': {
                                transform: 'scale(0.95)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'scale(1)',
                                opacity: '1'
                            }
                        },
                        'slide-up': {
                            '0%': {
                                transform: 'translateY(20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        },
                        'slide-down': {
                            '0%': {
                                transform: 'translateY(-20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        },
                        'fade-in': {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        }
                    },
                    backdropBlur: {
                        xs: '2px'
                    }
                }
            }
        }
    </script>
</head>