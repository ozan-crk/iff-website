// Uygulama Mantığı

document.addEventListener('DOMContentLoaded', () => {
    // Tüm sayfalarda çalışan ortak fonksiyonlar
    initSidePanel();
    initPopup();
    
    // Sadece ilgili elementlerin olduğu sayfalarda çalışan fonksiyonlar
    if (document.getElementById('slider-track')) initSlider();
    if (document.getElementById('mini-slider-track')) initMiniSlider();
    if (document.getElementById('program-content')) initCityTabs();
});

// Sol Panel Mantığı
function initSidePanel() {
    const panel = document.getElementById('side-panel');
    const toggleBtn = document.getElementById('toggle-panel');
    
    if (!panel || !toggleBtn) return;

    toggleBtn.addEventListener('click', () => {
        panel.classList.toggle('closed');
    });

    // Sayfa içindeki program içeriğini yan panele de kopyalayalım (ilk yükleme)
    /*
    if (document.getElementById('side-program-content')) {
        updateSideProgram('istanbul');
    }
    */
}

function updateSideProgram(city) {
    const sideContent = document.getElementById('side-program-content');
    if (!sideContent || !programData[city]) return;

    const data = programData[city][0]; // Sadece ilk filmi göster kompakt olması için
    
    sideContent.innerHTML = `
        <div class="border-l-4 border-orange pl-4 py-2 bg-cream/50 animate-fade-in">
            <p class="font-bold font-heading text-sm">${data.title}</p>
            <p class="text-xs text-gray-600 mt-1 uppercase tracking-tighter">${data.time} · ${data.place}</p>
            <p class="text-[10px] text-red mt-2 font-bold">${city.toUpperCase()}</p>
        </div>
    `;
}

// Global Program Verisi
const programData = {
    istanbul: [
        { title: 'Güneşli Günler', place: 'Şişli NHRKM', time: '19:30', date: '12 Mayıs 2024' },
        { title: 'Direniş Hatıraları', place: 'Kadıköy Sineması', time: '18:00', date: '13 Mayıs 2024' }
    ],
    ankara: [
        { title: 'Maden Yolunda', place: 'Büyülü Fener Sineması', time: '20:00', date: '14 Mayıs 2024' },
        { title: 'Fabrikada Bahar', place: 'Ankara Sanat Tiyatrosu', time: '17:30', date: '15 Mayıs 2024' }
    ],
    izmir: [
        { title: 'Ege\'nin Sesleri', place: 'İzmir Sanat', time: '19:00', date: '16 Mayıs 2024' },
        { title: 'Toprağın Canı', place: 'Karaca Sineması', time: '21:00', date: '17 Mayıs 2024' }
    ]
};

// Popup Duyuru Mantığı
function initPopup() {
    const popup = document.getElementById('announcement-popup');
    const closeBtns = document.querySelectorAll('#close-popup, #close-popup-btn');
    
    if (!popup) return;

    // Anasayfada popup göster (isteğe bağlı: sessionStorage ile sadece bir kez gösterilebilir)
    if (!sessionStorage.getItem('popupShown')) {
        setTimeout(() => {
            popup.classList.remove('opacity-0', 'pointer-events-none');
            popup.classList.add('opacity-100', 'pointer-events-auto');
            popup.querySelector('div').classList.remove('scale-95');
            popup.querySelector('div').classList.add('scale-100');
        }, 2000);
    }

    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            popup.classList.add('opacity-0', 'pointer-events-none');
            popup.classList.remove('opacity-100', 'pointer-events-auto');
            sessionStorage.setItem('popupShown', 'true');
        });
    });
}

// Ana Slider Mantığı
function initSlider() {
    const track = document.getElementById('slider-track');
    const dots = document.querySelectorAll('.slider-dot');
    if (!track || dots.length === 0) return;

    let currentIndex = 0;

    function goToSlide(index) {
        currentIndex = index;
        track.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => goToSlide(index));
    });

    setInterval(() => {
        currentIndex = (currentIndex + 1) % dots.length;
        goToSlide(currentIndex);
    }, 8000);
}

// Mini Foto Slider Mantığı
function initMiniSlider() {
    const track = document.getElementById('mini-slider-track');
    if (!track) return;

    let offset = 0;

    setInterval(() => {
        offset -= 320; // Kart genişliği + gap
        if (Math.abs(offset) >= track.scrollWidth - track.parentElement.clientWidth) {
            offset = 0;
        }
        track.style.transform = `translateX(${offset}px)`;
    }, 4000);
}

// Şehir Sekmeleri Mantığı
function initCityTabs() {
    const tabs = document.querySelectorAll('.city-tab');
    const content = document.getElementById('program-content');
    if (!tabs.length || !content) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const city = tab.dataset.city;
            
            tabs.forEach(t => {
                t.classList.remove('bg-red', 'text-white');
                t.classList.add('bg-white', 'text-warmgray');
            });
            tab.classList.add('bg-red', 'text-white');
            tab.classList.remove('bg-white', 'text-warmgray');

            renderProgram(city);
            updateSideProgram(city); // Yan paneli de güncelle
        });
    });

    function renderProgram(city) {
        const data = programData[city];
        content.innerHTML = `
            <div class="space-y-6 animate-fade-in">
                ${data.map(item => `
                    <div class="flex justify-between items-center border-b border-orange/20 pb-4">
                        <div>
                            <h4 class="font-bold text-lg font-heading">${item.title}</h4>
                            <p class="text-gray-600 font-serif text-sm">Yer: ${item.place}</p>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-orange">${item.time}</span>
                            <span class="text-xs text-gray-500 uppercase">${item.date}</span>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }
}
