<?php
// Redirect if there's a direct access attempt to prevent errors
if (isset($_POST['submit'])) {
    // If form is submitted, move the uploaded file to a temporary location
    if (isset($_FILES['file_excel']) && $_FILES['file_excel']['error'] == 0) {
        $tmp_file = $_FILES['file_excel']['tmp_name'];
        $target_file = 'uploads/' . basename($_FILES['file_excel']['name']);
        
        // Create uploads directory if it doesn't exist
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        // Move the uploaded file
        if (move_uploaded_file($tmp_file, $target_file)) {
            // Redirect to process.php with the file location
            header("Location: process.php?file=" . urlencode($target_file));
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK TOPSIS - Penerima Bantuan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        :root {
            --bg-primary: #0f0f13;
            --bg-secondary: #1a1a22;
            --accent: #9d4edd;
            --accent-light: #c77dff;
            --text: #f8f9fa;
            --text-muted: #b0b0b0;
            --shadow: rgba(0, 0, 0, 0.35);
            --card-border: rgba(255, 255, 255, 0.08);
            --hover-bg: #252535;
            --glow: 0 0 15px rgba(157, 78, 221, 0.5);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
        }
        
        body {
            background-color: var(--bg-primary);
            background-image: 
                radial-gradient(circle at 85% 10%, rgba(157, 78, 221, 0.1) 0%, transparent 25%),
                radial-gradient(circle at 15% 90%, rgba(157, 78, 221, 0.1) 0%, transparent 25%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .container {
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
            animation: fadeIn 0.8s cubic-bezier(0.22, 1, 0.36, 1);
            position: relative;
            z-index: 1;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(157, 78, 221, 0.05) 0%, transparent 70%);
            z-index: -1;
            pointer-events: none;
            animation: pulse 15s infinite alternate;
        }
        
        header {
            text-align: center;
            position: relative;
        }
        
        h1 {
            font-size: 3.5rem; /* Increased from 2.8rem */
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, var(--accent), var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
            text-shadow: 0 0 10px rgba(157, 78, 221, 0.3);
            position: relative;
            display: inline-block;
        }
        
        h1::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--accent), var(--accent-light));
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: right;
            animation: underline 1.5s ease-in-out forwards;
        }
        
        .subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 80%;
            margin: 0 auto;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out 0.3s forwards;
        }
        
        .card {
            background-color: var(--bg-secondary);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px var(--shadow), 
                        inset 0 1px 1px rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            gap: 2rem;
            transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
            backdrop-filter: blur(10px);
            background: rgba(26, 26, 34, 0.7);
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
            animation: cardEntrance 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.4s forwards;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(157, 78, 221, 0.1) 0%,
                rgba(157, 78, 221, 0) 50%
            );
            transform: rotate(30deg);
            z-index: -1;
            pointer-events: none;
        }
        
        .card:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 15px 35px var(--shadow),
                        var(--glow),
                        inset 0 1px 1px rgba(255, 255, 255, 0.05);
            border-color: rgba(157, 78, 221, 0.3);
        }
        
        input[type="file"] {
            display: none;
        }
        
        .file-upload-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            align-items: center;
        }
        
        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(15, 15, 19, 0.6);
            color: var(--text-muted);
            padding: 1.5rem;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
            text-align: center;
            border: 2px dashed rgba(157, 78, 221, 0.3);
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
            width: 100%;
            backdrop-filter: blur(5px);
        }
        
        .file-upload-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(157, 78, 221, 0.1),
                transparent
            );
            transition: all 0.6s ease;
        }
        
        .file-upload-label:hover::before {
            left: 100%;
        }
        
        .file-upload-label:hover {
            background-color: rgba(37, 37, 53, 0.6);
            color: var(--text);
            border-color: var(--accent);
            box-shadow: 0 0 15px rgba(157, 78, 221, 0.2);
        }
        
        .file-upload-label i {
            font-size: 1.8rem;
            color: var(--accent);
            transition: all 0.3s ease;
        }
        
        .file-upload-text {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .file-upload-label:hover i,
        .file-upload-label:hover .file-upload-text {
            transform: translateY(-2px);
        }
        
        .file-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: rgba(157, 78, 221, 0.08);
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.5s cubic-bezier(0.22, 1, 0.36, 1);
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            width: 100%;
            border: 1px solid rgba(157, 78, 221, 0.1);
            backdrop-filter: blur(5px);
        }
        
        .file-info.show {
            opacity: 1;
            max-height: 100px;
            margin-top: 0.5rem;
            animation: fileInfoAppear 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        }
        
        .file-icon {
            background: rgba(157, 78, 221, 0.15);
            padding: 0.75rem;
            border-radius: 10px;
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(157, 78, 221, 0.1);
        }
        
        .file-details {
            flex: 1;
            overflow: hidden;
        }
        
        .file-name {
            font-weight: 500;
            color: var(--text);
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 250px;
        }
        
        .file-size {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .file-clear {
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0.5rem;
            border-radius: 50%;
        }
        
        .file-cclear:hover {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            transform: rotate(90deg);
        }
        
        .button-container {
            display: flex;
            justify-content: center;
            width: 100%;
            position: relative;
        }
        
        button {
            background: linear-gradient(145deg, var(--accent), #7b2cbf);
            color: white;
            border: none;
            padding: 1.25rem 2.5rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
            box-shadow: 0 4px 15px rgba(157, 78, 221, 0.3);
            text-transform: uppercase;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            min-width: 220px;
            position: relative;
            overflow: hidden;
        }
        
        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: all 0.6s ease;
        }
        
        button:hover::before {
            left: 100%;
        }
        
        button:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(157, 78, 221, 0.5), var(--glow);
        }
        
        button:active {
            transform: translateY(1px) scale(0.98);
            box-shadow: 0 2px 10px rgba(157, 78, 221, 0.3);
        }
        
        button i {
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        button:hover i {
            transform: rotate(10deg);
        }
        
        button:disabled {
            background: linear-gradient(145deg, #6b6b6b, #4d4d4d);
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        button:disabled:hover::before {
            left: -100%;
        }
        
        /* Back button styles */
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(157, 78, 221, 0.1);
            color: var(--accent);
            border: 1px solid rgba(157, 78, 221, 0.3);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 10;
            backdrop-filter: blur(5px);
        }
        
        .back-button:hover {
            background: rgba(157, 78, 221, 0.2);
            color: var(--accent-light);
            border-color: var(--accent);
            transform: translateX(-3px);
        }
        
        .back-button i {
            transition: transform 0.3s ease;
        }
        
        .back-button:hover i {
            transform: translateX(-3px);
        }
        
        /* Floating particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
        }
        
        .particle {
            position: absolute;
            background: rgba(157, 78, 221, 0.3);
            border-radius: 50%;
            pointer-events: none;
            animation: float linear infinite;
        }
        
        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 15, 19, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease, visibility 0.4s ease;
            backdrop-filter: blur(10px);
        }
        
        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .spinner-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin-bottom: 2rem;
        }
        
        .spinner {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid transparent;
            border-top-color: var(--accent);
            animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            filter: drop-shadow(0 0 5px var(--accent));
        }
        
        .spinner:nth-child(2) {
            border-top-color: transparent;
            border-right-color: var(--accent-light);
            animation-delay: -0.3s;
        }
        
        .spinner:nth-child(3) {
            width: 80%;
            height: 80%;
            top: 10%;
            left: 10%;
            border-top-color: transparent;
            border-left-color: var(--accent);
            animation-delay: -0.6s;
        }
        
        .loading-text {
            margin-top: 1rem;
            font-size: 1.2rem;
            color: var(--text);
            text-align: center;
            font-weight: 500;
            letter-spacing: 0.5px;
            opacity: 0;
            animation: fadeIn 0.5s ease-out 0.3s forwards;
        }
        
        .loading-progress {
            width: 80%;
            max-width: 300px;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-top: 2rem;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        .progress-bar {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--accent), var(--accent-light));
            border-radius: 10px;
            transition: width 0.4s ease;
            animation: progressAnimation 2s infinite ease-in-out;
            box-shadow: 0 0 10px rgba(157, 78, 221, 0.5);
        }
        
        footer {
            margin-top: 2rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            text-align: center;
            opacity: 0;
            animation: fadeIn 0.8s ease-out 0.6s forwards;
        }
        
        /* Keyframes */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes progressAnimation {
            0% { width: 0%; opacity: 1; }
            50% { width: 100%; opacity: 1; }
            100% { width: 0%; opacity: 0; }
        }
        
        @keyframes float {
            0% { transform: translateY(0) translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) translateX(100px); opacity: 0; }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 0.5; }
            100% { transform: scale(1); opacity: 0.8; }
        }
        
        @keyframes underline {
            0% { transform: scaleX(0); transform-origin: right; }
            50% { transform: scaleX(1); transform-origin: right; }
            51% { transform-origin: left; }
            100% { transform: scaleX(0); transform-origin: left; }
        }
        
        @keyframes fileInfoAppear {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        @media (max-width: 640px) {
            body {
                padding: 1.5rem 1rem;
            }
            
            .card {
                padding: 1.75rem;
            }
            
            h1 {
                font-size: 2.8rem; /* Increased from 2.2rem */
            }
            
            .subtitle {
                font-size: 0.95rem;
                max-width: 95%;
            }
            
            button {
                padding: 1.1rem 2rem;
                min-width: 180px;
            }
            
            .back-button {
                top: 10px;
                left: 10px;
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Back button -->
    <a href="index.html" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Kembali
    </a>
    
    <!-- Floating particles -->
    <div class="particles" id="particles"></div>
    
    <div class="container">
        <header>
            <h1>SPK TOPSIS</h1>
            <p class="subtitle">Sistem Pendukung Keputusan untuk Penentuan Penerima Bantuan</p>
        </header>
        
        <div class="card">
            <!-- Form untuk mengunggah file Excel -->
            <form method="post" enctype="multipart/form-data" id="upload-form" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="file-upload-container">
                    <label for="file_excel" class="file-upload-label" id="upload-label">
                        <i class="fas fa-file-excel"></i>
                        <span class="file-upload-text">Pilih File Excel atau drag & drop di sini</span>
                    </label>
                    
                    <div class="file-info" id="file-info">
                        <div class="file-icon">
                            <i class="fas fa-file-excel"></i>
                        </div>
                        <div class="file-details">
                            <div class="file-name" id="file-name-display">Filename.xlsx</div>
                            <div class="file-size" id="file-size-display">0 KB</div>
                        </div>
                        <div class="file-clear" id="file-clear" title="Hapus file">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                    
                    <div class="button-container">
                        <button type="submit" name="submit" id="submit-btn" disabled>
                            <i class="fas fa-cogs"></i>
                            Proses Data
                        </button>
                    </div>
                    
                    <input type="file" name="file_excel" id="file_excel" required accept=".xlsx,.xls">
                </div>
            </form>
        </div>
        
        <footer>
            &copy; <?php echo date('Y'); ?> SPK TOPSIS - Penerima Bantuan
        </footer>
    </div>
    
    <!-- Loading overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="spinner-container">
            <div class="spinner"></div>
            <div class="spinner"></div>
            <div class="spinner"></div>
        </div>
        <div class="loading-text">Memproses Data, Mohon Tunggu...</div>
        <div class="loading-progress">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
    </div>

    <script>
        // Format file size function
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Handle file selection
        const fileInput = document.getElementById('file_excel');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name-display');
        const fileSize = document.getElementById('file-size-display');
        const fileClear = document.getElementById('file-clear');
        const uploadLabel = document.getElementById('upload-label');
        const submitBtn = document.getElementById('submit-btn');
        
        // Handle file input change
        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                const file = this.files[0];
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.classList.add('show');
                uploadLabel.style.bborderStyle = 'solid';
                uploadLabel.style.borderColor = 'var(--accent)';
                submitBtn.disabled = false;
                
                // Add glow effect to button
                submitBtn.style.boxShadow = '0 0 15px rgba(157, 78, 221, 0.5)';
                setTimeout(() => {
                    submitBtn.style.boxShadow = '0 4px 15px rgba(157, 78, 221, 0.3)';
                }, 1000);
            } else {
                resetFileInput();
            }
        });
        
        // Handle file clear
        fileClear.addEventListener('click', function(e) {
            e.preventDefault();
            resetFileInput();
        });
        
        // Reset file input
        function resetFileInput() {
            fileInput.value = '';
            fileInfo.classList.remove('show');
            uploadLabel.style.borderStyle = 'dashed';
            uploadLabel.style.borderColor = 'rgba(157, 78, 221, 0.3)';
            submitBtn.disabled = true;
        }
        
        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadLabel.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadLabel.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadLabel.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            uploadLabel.style.borderColor = 'var(--accent)';
            uploadLabel.style.backgroundColor = 'rgba(37, 37, 53, 0.6)';
            uploadLabel.style.boxShadow = '0 0 15px rgba(157, 78, 221, 0.2)';
        }
        
        function unhighlight() {
            uploadLabel.style.borderColor = 'rgba(157, 78, 221, 0.3)';
            uploadLabel.style.backgroundColor = 'rgba(15, 15, 19, 0.6)';
            uploadLabel.style.boxShadow = 'none';
        }
        
        uploadLabel.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0 && (files[0].name.endsWith('.xlsx') || files[0].name.endsWith('.xls'))) {
                fileInput.files = files;
                const file = files[0];
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.classList.add('show');
                uploadLabel.style.borderStyle = 'solid';
                uploadLabel.style.borderColor = 'var(--accent)';
                submitBtn.disabled = false;
            }
        }
        
        // Form submission and loading overlay
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            if (fileInput.files.length > 0) {
                const loadingOverlay = document.getElementById('loading-overlay');
                loadingOverlay.classList.add('show');
                
                // Simulated progress for visual feedback
                let progress = 0;
                const progressBar = document.getElementById('progress-bar');
                
                const progressInterval = setInterval(() => {
                    progress += Math.random() * 15;
                    
                    if (progress > 70) {
                        clearInterval(progressInterval);
                    } else {
                        progressBar.style.width = progress + '%';
                    }
                }, 500);
            }
        });
        
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 15;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random size between 2px and 6px
                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random position
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                
                // Random animation duration between 10s and 20s
                const duration = Math.random() * 10 + 10;
                particle.style.animationDuration = `${duration}s`;
                
                // Random delay
                particle.style.animationDelay = `${Math.random() * 5}s`;
                
                particlesContainer.appendChild(particle);
            }
        }
        
        // Initialize particles when page loads
        window.addEventListener('load', createParticles);
    </script>
</body>
</html>