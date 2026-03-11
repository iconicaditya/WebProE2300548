/**
 * EduSkill Custom JavaScript
 * Advanced animations and interactive features
 */

// Typing Animation for Hero Section
class TypingAnimation {
    constructor(elementId, text, speed = 70, delay = 500) {
        this.element = document.getElementById(elementId);
        this.fullText = text;
        this.displayText = '';
        this.speed = speed; // milliseconds per character
        this.delay = delay; // initial delay before typing starts
        this.deleteSpeed = 50; // speed of deleting characters
        this.pauseTime = 2500; // pause time at end before deleting
        this.currentIndex = 0;
        this.isTyping = false;
        this.isDeleting = false;
    }

    start() {
        if (!this.element) return;
        
        // Clear initial content
        this.element.textContent = '';
        
        // Add cursor styling
        this.element.classList.add('typing-active');
        
        // Start typing after initial delay
        setTimeout(() => this.type(), this.delay);
    }

    type() {
        if (this.currentIndex < this.fullText.length) {
            this.isTyping = true;
            this.displayText += this.fullText[this.currentIndex];
            this.element.textContent = this.displayText;
            this.currentIndex++;
            
            setTimeout(() => this.type(), this.speed);
        } else {
            this.isTyping = false;
            this.element.classList.remove('typing-active');
            this.element.classList.add('typing-complete');
            
            // Wait before deleting
            setTimeout(() => this.delete(), this.pauseTime);
        }
    }

    delete() {
        if (this.currentIndex > 0) {
            this.isDeleting = true;
            this.displayText = this.displayText.slice(0, -1);
            this.element.textContent = this.displayText;
            this.currentIndex--;
            this.element.classList.add('typing-active');
            this.element.classList.remove('typing-complete');
            
            setTimeout(() => this.delete(), this.deleteSpeed);
        } else {
            this.isDeleting = false;
            // Pause before typing again
            setTimeout(() => this.type(), 500);
        }
    }

    reset() {
        this.currentIndex = 0;
        this.displayText = '';
        this.element.textContent = '';
        this.element.classList.remove('typing-active', 'typing-complete');
        this.start();
    }
}

// Initialize typing animation when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const typingElement = document.getElementById('typingText');
    
    if (typingElement) {
        const typingText = 'the Digital Age';
        const typing = new TypingAnimation('typingText', typingText, 70, 800);
        typing.start();
    }
});

// Optional: Restart animation on scroll into view
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && entry.target.id === 'typingText') {
                // Animation already started, but you can add additional logic here
            }
        });
    }, observerOptions);

    const typingElement = document.getElementById('typingText');
    if (typingElement) {
        observer.observe(typingElement);
    }
});
