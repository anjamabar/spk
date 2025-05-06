<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Social Media | Contact</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #020617 100%);
            min-height: 100vh;
            color: white;
            overflow-x: hidden;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            transition: all 0.4s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(124, 58, 237, 0.4);
            background: rgba(124, 58, 237, 0.1);
        }
        
        .social-icon {
            transition: all 0.4s ease;
        }
        
        .social-icon:hover {
            transform: scale(1.15) rotate(8deg);
        }
        
        .glow {
            animation: glow 3s infinite alternate;
        }
        
        @keyframes glow {
            from {
                box-shadow: 0 0 10px rgba(124, 58, 237, 0.5);
            }
            to {
                box-shadow: 0 0 25px rgba(124, 58, 237, 0.9);
            }
        }
        
        .floating {
            animation: floating 4s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(3deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.08); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            transform: translateX(-5px);
        }

        .ripple-effect {
            position: absolute;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: translate(-50%, -50%) scale(10);
                opacity: 0;
            }
        }
        
        .animate-fadeIn {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.8s ease-out forwards;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slideIn {
            opacity: 0;
            transform: translateX(-20px);
            animation: slideIn 0.8s ease-out forwards;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        }

        .card-hover-effect:hover .card-icon {
            transform: translateY(-8px) scale(1.1);
        }

        .card-icon {
            transition: all 0.4s ease;
        }

        .special-text {
            background: linear-gradient(90deg, #7c3aed, #d946ef, #7c3aed);
            background-size: 200% auto;
            background-clip: text;
            -webkit-background-clip: text;
            text-fill-color: transparent;
            -webkit-text-fill-color: transparent;
            animation: gradient 3s linear infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>

    <a href="index.html" class="back-button bg-white/10 hover:bg-white/20 p-3 rounded-full backdrop-blur-sm animate-slideIn">
        <i class="fas fa-arrow-left text-purple-400 text-lg"></i>
    </a>

    <div class="container mx-auto max-w-4xl px-4 py-16">
        <div class="text-center mb-16 animate-fadeIn" style="animation-delay: 0.2s;">
            <div class="floating inline-block mb-8">
                <img src="images/kontak_profil.jpg" alt="Profile" class="w-36 h-36 rounded-full border-4 border-purple-500 glow object-cover mx-auto shadow-lg shadow-purple-500/20">
            </div>
            <h1 class="text-5xl font-bold mb-3 special-text">Arin</h1>
            <p class="text-gray-300 max-w-lg mx-auto text-lg">Digital Creator | Content Specialist | Social Media Enthusiast</p>
            <p class="text-purple-300 mt-2">Mahasiswa Universitas Sembilan Belas November</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
            <a href="https://www.facebook.com/arin.koltim.869" target="_blank" class="card p-6 hover:bg-gradient-to-br from-purple-900/30 to-black/50 card-hover-effect animate-fadeIn" style="animation-delay: 0.3s;">
                <div class="flex items-center">
                    <div class="card-icon social-icon w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center text-white text-2xl mr-5 shadow-lg shadow-blue-500/20">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">Facebook</h3>
                        <p class="text-gray-400">@アリン</p>
                    </div>
                </div>
            </a>
            
    
            <a href="https://www.instagram.com/arin.koltim.869/" target="_blank" class="card p-6 hover:bg-gradient-to-br from-purple-900/30 to-black/50 card-hover-effect animate-fadeIn" style="animation-delay: 0.4s;">
                <div class="flex items-center">
                    <div class="card-icon social-icon w-16 h-16 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 flex items-center justify-center text-white text-2xl mr-5 shadow-lg shadow-pink-500/20">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">Instagram</h3>
                        <p class="text-gray-400">@アリン</p>
                    </div>
                </div>
            </a>
        
            <a href="https://www.tiktok.com/@palabapakau92?_t=ZS-8w5PL6jvc1f&_r=1" target="_blank" class="card p-6 hover:bg-gradient-to-br from-purple-900/30 to-black/50 card-hover-effect animate-fadeIn" style="animation-delay: 0.5s;">
                <div class="flex items-center">
                    <div class="card-icon social-icon w-16 h-16 rounded-full bg-black flex items-center justify-center text-white text-2xl mr-5 shadow-lg shadow-slate-700/30">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">TikTok</h3>
                        <p class="text-gray-400">@palabapakau92</p>
                    </div>
                </div>
            </a>
            
    
            <a href="https://twitter.com" target="_blank" class="card p-6 hover:bg-gradient-to-br from-purple-900/30 to-black/50 card-hover-effect animate-fadeIn" style="animation-delay: 0.6s;">
                <div class="flex items-center">
                    <div class="card-icon social-icon w-16 h-16 rounded-full bg-blue-400 flex items-center justify-center text-white text-2xl mr-5 shadow-lg shadow-blue-400/20">
                        <i class="fab fa-twitter"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">Twitter</h3>
                        <p class="text-gray-400">@Arkan__</p>
                    </div>
                </div>
            </a>
            
        
            <a href="https://www.youtube.com/channel/UCMdJr7Y2Mf6REr5sd209i2A" target="_blank" class="card p-6 hover:bg-gradient-to-br from-purple-900/30 to-black/50 card-hover-effect animate-fadeIn" style="animation-delay: 0.7s;">
                <div class="flex items-center">
                    <div class="card-icon social-icon w-16 h-16 rounded-full bg-red-600 flex items-center justify-center text-white text-2xl mr-5 shadow-lg shadow-red-500/20">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">YouTube</h3>
                        <p class="text-gray-400">@manusiaberak</p>
                    </div>
                </div>
            </a>
            

            <a href="https://linkedin.com" target="_blank" class="card p-6 hover:bg-gradient-to-br from-purple-900/30 to-black/50 card-hover-effect animate-fadeIn" style="animation-delay: 0.8s;">
                <div class="flex items-center">
                    <div class="card-icon social-icon w-16 h-16 rounded-full bg-blue-700 flex items-center justify-center text-white text-2xl mr-5 shadow-lg shadow-blue-700/20">
                        <i class="fab fa-linkedin-in"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">LinkedIn</h3>
                        <p class="text-gray-400">@Arkan</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="mt-16 text-center animate-fadeIn" style="animation-delay: 0.9s;">
            <h2 class="text-3xl font-semibold mb-5 special-text">Get in Touch</h2>
            <p class="text-gray-300 mb-8 text-lg">Feel free to reach out through any of my social media platforms</p>
            <div class="pulse inline-block">
                <a href="mailto:arinkoltim1140@gmail.com" onclick="openMailForm(event)" class="px-8 py-4 bg-gradient-purple hover:opacity-90 rounded-full font-medium transition-all duration-300 inline-flex items-center shadow-lg shadow-purple-500/30">
                    <i class="fas fa-envelope mr-3"></i> Email Me
                </a>
            </div>
        </div>
        
        <div class="mt-16 text-center text-gray-500 text-sm animate-fadeIn" style="animation-delay: 1s;">
            <p>© 2025 Arin. All rights reserved.</p>
        </div>
    </div>
    

    <div id="emailModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center">
        <div class="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-purple-500/30 shadow-xl animate-fadeIn">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-white">Send Email to Arin</h3>
                <button onclick="closeEmailModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="emailForm" action="https://mail.google.com/mail/" method="GET" target="_blank">
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-300 mb-1">Subject</label>
                    <input type="text" id="subject" name="subject" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Enter subject">
                </div>
                <div class="mb-6">
                    <label for="body" class="block text-sm font-medium text-gray-300 mb-1">Message</label>
                    <textarea id="body" name="body" rows="5" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Type your message here..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEmailModal()" class="px-4 py-2 bg-slate-600 text-white rounded-lg mr-2 hover:bg-slate-500">Cancel</button>
                    <button type="submit" onclick="sendEmail()" class="px-4 py-2 bg-gradient-purple text-white rounded-lg hover:opacity-90">Send Email</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', function(e) {
                let x = e.clientX - e.target.getBoundingClientRect().left;
                let y = e.clientY - e.target.getBoundingClientRect().top;
                
                let ripple = document.createElement('span');
                ripple.classList.add('ripple-effect');
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 1000);
            });
        });
        

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = "1";
                        entry.target.style.transform = "translateY(0)";
                    }, index * 100);
                }
            });
        }, {threshold: 0.1});
        
    
        function openMailForm(e) {
            e.preventDefault();
            document.getElementById('emailModal').classList.remove('hidden');
        }
        
        function closeEmailModal() {
            document.getElementById('emailModal').classList.add('hidden');
        }
        
        function sendEmail() {
            let form = document.getElementById('emailForm');
            let subject = document.getElementById('subject').value;
            let body = document.getElementById('body').value;
            
           
            let mailtoUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=arinkoltim1140@gmail.com&su=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            
           
            window.open(mailtoUrl, '_blank');
            
           
            closeEmailModal();
        }
        
 
        window.addEventListener('load', function() {
            document.querySelectorAll('.animate-fadeIn').forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = "1";
                    el.style.transform = "translateY(0)";
                }, 200 + index * 100);
            });
        });
        
   
        window.addEventListener('click', function(e) {
            if (e.target === document.getElementById('emailModal')) {
                closeEmailModal();
            }
        });
        
        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });
        document.querySelectorAll('.card-icon').forEach(icon => {
            icon.style.animationDelay = `${Math.random() * 2}s`;
        });
    </script>
</body>
</html>