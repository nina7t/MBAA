/**
 * JavaScript pour la page Le Musée
 * Animations et interactions pour la timeline, galerie et autres éléments
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Animation de la timeline au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observer les éléments de la timeline
    document.querySelectorAll('.timeline__item').forEach(item => {
        observer.observe(item);
    });
    
    // Observer les cartes de mécènes
    document.querySelectorAll('.mecene__card').forEach(card => {
        observer.observe(card);
    });
    
    // Galerie d'images pour la section architecture
    const galleryMain = document.querySelector('.gallery__main img');
    const galleryThumbs = document.querySelectorAll('.gallery__thumbs img');
    
    if (galleryMain && galleryThumbs.length > 0) {
        galleryThumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', function() {
                // Changer l'image principale
                const tempSrc = galleryMain.src;
                galleryMain.src = this.src;
                this.src = tempSrc;
                
                // Ajouter un effet de transition
                galleryMain.style.opacity = '0';
                setTimeout(() => {
                    galleryMain.style.opacity = '1';
                }, 100);
            });
        });
    }
    
    // Animation des statistiques de restauration
    const statsNumbers = document.querySelectorAll('.stat__number');
    const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                const finalValue = entry.target.textContent;
                const isDecimal = finalValue.includes('.');
                const suffix = finalValue.replace(/[0-9.,]/g, '');
                const numericValue = parseFloat(finalValue.replace(/[^0-9.]/g, ''));
                
                let currentValue = 0;
                const increment = numericValue / 50;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= numericValue) {
                        currentValue = numericValue;
                        clearInterval(timer);
                        entry.target.classList.add('animated');
                    }
                    
                    if (isDecimal) {
                        entry.target.textContent = currentValue.toFixed(1) + suffix;
                    } else {
                        entry.target.textContent = Math.floor(currentValue) + suffix;
                    }
                }, 30);
            }
        });
    }, { threshold: 0.5 });
    
    statsNumbers.forEach(stat => {
        statsObserver.observe(stat);
    });
    
    // Smooth scroll pour les ancres
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Effet parallaxe subtil pour le hero
    const hero = document.querySelector('.header__hero');
    if (hero) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = hero.querySelector('.hero__left');
            if (parallax) {
                const speed = 0.5;
                parallax.style.transform = `translateY(${scrolled * speed}px)`;
            }
        });
    }
    
    // Animation d'apparition pour les sections
    const sections = document.querySelectorAll('.musee__section');
    const sectionObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('section-visible');
            }
        });
    }, { threshold: 0.1 });
    
    sections.forEach(section => {
        sectionObserver.observe(section);
    });
    
    // Gestion du hover sur les cartes de mécènes avec effet de profondeur
    const meceneCards = document.querySelectorAll('.mecene__card');
    meceneCards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
        });
    });
    
    // Timeline navigation
    const timelineDates = document.querySelectorAll('.timeline__date');
    timelineDates.forEach((date, index) => {
        date.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const allContents = document.querySelectorAll('.timeline__content');
            
            // Toggle current content
            content.classList.toggle('expanded');
            
            // Close others (optional - remove for multiple open)
            allContents.forEach((otherContent, otherIndex) => {
                if (otherIndex !== index) {
                    otherContent.classList.remove('expanded');
                }
            });
        });
    });
    
    // Bouton retour en haut
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '↑';
    backToTop.className = 'back-to-top';
    backToTop.setAttribute('aria-label', 'Retour en haut');
    document.body.appendChild(backToTop);
    
    // Styles pour le bouton retour en haut
    const backToTopStyles = `
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #957e18;
            color: #FFFDF3;
            border: none;
            font-size: 20px;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            background: #7d6a14;
            transform: translateY(-3px);
        }
    `;
    
    const styleSheet = document.createElement('style');
    styleSheet.textContent = backToTopStyles;
    document.head.appendChild(styleSheet);
    
    // Afficher/masquer le bouton retour en haut
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });
    
    // Action du bouton retour en haut
    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Animation des feature items
    const featureItems = document.querySelectorAll('.feature__item');
    const featureObserver = new IntersectionObserver(function(entries) {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('feature-animate');
                }, index * 100);
            }
        });
    }, { threshold: 0.3 });
    
    featureItems.forEach(item => {
        featureObserver.observe(item);
    });
    
    // Styles pour les animations
    const animationStyles = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .timeline__item.animate-in:nth-child(odd) .timeline__content {
            animation: slideInLeft 0.6s ease forwards;
        }
        
        .timeline__item.animate-in:nth-child(even) .timeline__content {
            animation: slideInRight 0.6s ease forwards;
        }
        
        .mecene__card.animate-in {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .feature-item.feature-animate {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        .timeline__content,
        .mecene__card,
        .feature__item {
            opacity: 0;
        }
        
        .timeline__item.animate-in .timeline__content,
        .mecene__card.animate-in,
        .feature__item.feature-animate {
            opacity: 1;
        }
        
        .gallery__main img {
            transition: opacity 0.3s ease;
        }
        
        .timeline__content.expanded {
            max-height: 500px;
            overflow: visible;
        }
        
        .timeline__content {
            max-height: 300px;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
    `;
    
    const animStyleSheet = document.createElement('style');
    animStyleSheet.textContent = animationStyles;
    document.head.appendChild(animStyleSheet);
    
});
