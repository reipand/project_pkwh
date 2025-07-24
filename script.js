const slider = document.getElementById('slider');
const slides = slider.children;
const totalSlides = slides.length;
let currentIndex = 0;

const dotsContainer = document.getElementById('dots');

// Buat dot sebanyak slide
for (let i = 0; i < totalSlides; i++) {
  const dot = document.createElement('div');
  dot.classList.add('dot');
  if (i === 0) dot.classList.add('active');
  dot.addEventListener('click', () => showSlide(i));
  dotsContainer.appendChild(dot);
}

function updateDots(index) {
  const allDots = dotsContainer.querySelectorAll('.dot');
  allDots.forEach(dot => dot.classList.remove('active'));
  allDots[index].classList.add('active');
}

function showSlide(index) {
  if (index < 0) index = totalSlides - 1;
  if (index >= totalSlides) index = 0;
  slider.style.transform = `translateX(-${index * 100}vw)`;
  currentIndex = index;
  updateDots(index);
}

document.getElementById('nextBtn').addEventListener('click', () => showSlide(currentIndex + 1));
document.getElementById('prevBtn').addEventListener('click', () => showSlide(currentIndex - 1));

// Auto slide every 5 seconds
setInterval(() => {
  showSlide(currentIndex + 1);
}, 5000);