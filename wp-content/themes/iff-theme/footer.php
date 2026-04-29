<?php
/**
 * Tema Alt Kısmı (Footer)
 */
?>
    <!-- Personal Program Floating UI (Şimdilik Gizli) -->
    <div id="personal-program-bar" style="display: none !important;" class="fixed bottom-0 left-0 right-0 bg-red text-white py-4 px-6 transform translate-y-full transition-transform duration-500 z-[100] shadow-2xl flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="bg-white text-red w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg shadow-inner" id="personal-program-count">0</div>
            <div>
                <h4 class="font-heading font-bold text-sm uppercase tracking-wider">Kişisel Programınız</h4>
                <p class="text-[10px] text-white/70 uppercase tracking-widest">Takviminize eklediğiniz seanslar burada listelenir.</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button id="clear-personal-program" class="px-4 py-2 text-[10px] uppercase font-bold tracking-widest hover:bg-white/10 transition-colors">Temizle</button>
            <button id="download-personal-calendar" class="bg-white text-red px-6 py-2 rounded-sm font-heading font-bold text-xs uppercase tracking-widest hover:bg-cream transition-all modern-shadow">Takvime Aktar (.ICS)</button>
        </div>
    </div>

    <footer class="bg-warmgray py-8 px-6 text-center border-t border-white/10 mt-auto pb-24 md:pb-8">
        <p class="text-gray-400 text-xs font-serif uppercase tracking-widest">&copy; <?php echo date('Y'); ?> IFF - İşçi Filmleri Festivali. Tüm hakları saklıdır.</p>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let program = JSON.parse(localStorage.getItem('iff_personal_program') || '[]');
        const bar = document.getElementById('personal-program-bar');
        const countDisplay = document.getElementById('personal-program-count');
        const downloadBtn = document.getElementById('download-personal-calendar');
        const clearBtn = document.getElementById('clear-personal-program');

        function updateUI() {
            countDisplay.textContent = program.length;
            if (program.length > 0) {
                bar.classList.remove('translate-y-full');
            } else {
                bar.classList.add('translate-y-full');
            }

            // Update all buttons on page
            document.querySelectorAll('.add-to-personal-program').forEach(btn => {
                const id = btn.dataset.id;
                if (program.some(item => item.id === id)) {
                    btn.classList.add('is-added');
                    btn.querySelector('span').textContent = 'Eklendi';
                } else {
                    btn.classList.remove('is-added');
                    btn.querySelector('span').textContent = 'Takvime Ekle';
                }
            });
        }

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.add-to-personal-program');
            if (btn) {
                const item = {
                    id: btn.dataset.id,
                    title: btn.dataset.title,
                    date: btn.dataset.date,
                    time: btn.dataset.time,
                    venue: btn.dataset.venue,
                    duration: btn.dataset.duration
                };

                const index = program.findIndex(p => p.id === item.id);
                if (index === -1) {
                    program.push(item);
                } else {
                    program.splice(index, 1);
                }

                localStorage.setItem('iff_personal_program', JSON.stringify(program));
                updateUI();
            }
        });

        clearBtn.addEventListener('click', () => {
            program = [];
            localStorage.setItem('iff_personal_program', '[]');
            updateUI();
        });

        downloadBtn.addEventListener('click', () => {
            if (program.length === 0) return;

            const escapeICS = (str) => {
                if (!str) return "";
                return str.replace(/[\\,;]/g, (match) => `\\${match}`)
                          .replace(/\n/g, '\\n');
            };

            let icsContent = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//IFF//Festival Program//TR\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\n";
            
            program.forEach(item => {
                // Date format: 01.05.2026 -> 20260501
                const dateParts = item.date.split('.');
                const formattedDate = dateParts[2] + dateParts[1] + dateParts[0];
                
                // Time format: 19:30 -> 193000
                const timeParts = item.time.split(':');
                const formattedTime = timeParts[0] + timeParts[1] + "00";
                
                // Duration
                const durationMinutes = parseInt(item.duration) || 90;
                const endDateObj = new Date(`${dateParts[2]}-${dateParts[1]}-${dateParts[0]}T${item.time}:00`);
                endDateObj.setMinutes(endDateObj.getMinutes() + durationMinutes);
                
                const endFormattedTime = endDateObj.getHours().toString().padStart(2, '0') + 
                                       endDateObj.getMinutes().toString().padStart(2, '0') + "00";

                icsContent += "BEGIN:VEVENT\r\n";
                icsContent += `UID:${item.id}-${Date.now()}@iff.org.tr\r\n`;
                icsContent += `DTSTAMP:${new Date().toISOString().replace(/[-:]/g, '').split('.')[0]}Z\r\n`;
                icsContent += `DTSTART:${formattedDate}T${formattedTime}\r\n`;
                icsContent += `DTEND:${formattedDate}T${endFormattedTime}\r\n`;
                icsContent += `SUMMARY:${escapeICS(item.title)}\r\n`;
                icsContent += `LOCATION:${escapeICS(item.venue)}\r\n`;
                icsContent += "DESCRIPTION:İşçi Filmleri Festivali Gösterimi\\nKişisel Festival Programınızdan oluşturuldu.\r\n";
                icsContent += "END:VEVENT\r\n";
            });

            icsContent += "END:VCALENDAR";

            // Cihaz Tespiti
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

            if (isIOS) {
                // iPhone için: Veriyi PHP'ye POST et (Bu sayede Takvim otomatik açılır)
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = window.location.href;
                
                const inputContent = document.createElement('input');
                inputContent.type = 'hidden';
                inputContent.name = 'ics_content';
                inputContent.value = icsContent;
                
                const inputAction = document.createElement('input');
                inputAction.type = 'hidden';
                inputAction.name = 'download_ics';
                inputAction.value = '1';
                
                form.appendChild(inputContent);
                form.appendChild(inputAction);
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            } else {
                // Diğer cihazlar (Android/Masaüstü): Standart Blob indirme
                const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.setAttribute('download', 'IFF_Kisisel_Programim.ics');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });

        updateUI();
    });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
