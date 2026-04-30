<?php
/**
 * Volunteer Form Block Template.
 */

$form_title = get_field('form_basligi') ?: 'Gönüllümüz Olun';
$form_description = get_field('form_aciklamasi') ?: 'İşçi Filmleri Festivali, gönüllülerin kolektif emeğiyle var olan bir festivaldir. Siz de bu dayanışmanın bir parçası olabilirsiniz.';

$id = 'volunteer-form-' . $block['id'];
$className = 'volunteer-form-block';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}
?>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?> py-16">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex flex-col md:flex-row bg-white border-8 border-orange modern-shadow relative">

            <!-- Sol Panel: Bilgi ve Başlık -->
            <div class="md:w-1/3 bg-orange p-8 md:p-12 text-white flex flex-col justify-between">
                <div>

                    <h2 class="text-4xl font-heading font-bold uppercase leading-none mb-6">
                        <?php echo esc_html($form_title); ?>
                    </h2>
                    <p class="text-white text-opacity-90 font-serif leading-relaxed">
                        <?php echo esc_html($form_description); ?>
                    </p>
                </div>

                <div class="mt-12 hidden md:block">
                    <p class="text-sm italic font-serif">Uluslararası İşçi Filmleri Festivali Gönüllüsü Olun!</p>
                </div>
            </div>

            <!-- Sağ Panel: Form -->
            <div class="md:w-2/3 p-8 md:p-12 bg-white">
                <form action="#" method="POST" class="space-y-8">
                    <!-- Kişisel Bilgiler -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase text-gray-400">Ad</label>
                            <input type="text" name="first_name" required
                                class="v-input w-full border-b-2 border-gray-100 focus:border-orange outline-none py-2 text-lg transition-all"
                                placeholder="Adınız">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase text-gray-400">Soyad</label>
                            <input type="text" name="last_name" required
                                class="v-input w-full border-b-2 border-gray-100 focus:border-orange outline-none py-2 text-lg transition-all"
                                placeholder="Soyadınız">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase text-gray-400">E-Posta</label>
                            <input type="email" name="email" required
                                class="v-input w-full border-b-2 border-gray-100 focus:border-orange outline-none py-2 text-lg transition-all"
                                placeholder="eposta@adresiniz.com">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase text-gray-400">Telefon</label>
                            <input type="tel" name="phone" required
                                class="v-input w-full border-b-2 border-gray-100 focus:border-orange outline-none py-2 text-lg transition-all"
                                placeholder="05xx xxx xx xx">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase text-gray-400">Şehir</label>
                            <input type="text" name="city" required
                                class="v-input w-full border-b-2 border-gray-100 focus:border-orange outline-none py-2 text-lg transition-all"
                                placeholder="Yaşadığınız şehir">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold uppercase text-gray-400">Meslek / Öğrenci</label>
                            <input type="text" name="occupation"
                                class="v-input w-full border-b-2 border-gray-100 focus:border-orange outline-none py-2 text-lg transition-all"
                                placeholder="Ne ile meşgulsünüz?">
                        </div>
                    </div>

                    <!-- İlgi Alanları -->
                    <div class="space-y-4 pt-4">
                        <label
                            class="block text-xs font-bold uppercase text-warmgray border-l-4 border-orange pl-3">Destek
                            Olmak İstediğiniz Alanlar</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="checkbox" name="interests[]" value="organizasyon"
                                    class="w-5 h-5 accent-orange">
                                <span
                                    class="text-sm text-gray-600 group-hover:text-orange transition-colors">Organizasyon
                                    ve Planlama</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="checkbox" name="interests[]" value="sosyal-medya"
                                    class="w-5 h-5 accent-orange">
                                <span class="text-sm text-gray-600 group-hover:text-orange transition-colors">Sosyal
                                    Medya ve Tanıtım</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="checkbox" name="interests[]" value="teknik" class="w-5 h-5 accent-orange">
                                <span class="text-sm text-gray-600 group-hover:text-orange transition-colors">Teknik
                                    Destek (Gösterimler)</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="checkbox" name="interests[]" value="ceviri" class="w-5 h-5 accent-orange">
                                <span class="text-sm text-gray-600 group-hover:text-orange transition-colors">Çeviri ve
                                    Altyazı</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold uppercase text-gray-400">Ek Notlar</label>
                        <textarea name="notes" rows="3"
                            class="v-input w-full border-b-2 border-gray-100 focus:border-orange outline-none py-2 text-lg transition-all resize-none"
                            placeholder="Kendinizden biraz bahseder misiniz?"></textarea>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="submit-btn w-full bg-warmgray text-white py-5 font-heading font-bold uppercase tracking-widest hover:bg-orange transition-all duration-500 transform hover:scale-[1.02]">
                            <span class="btn-text">Başvuruyu Tamamla</span>
                        </button>
                    </div>
                </form>

                <div id="v-form-success-<?php echo esc_attr($block['id']); ?>"
                    class="hidden py-20 text-center animate-fade-in">
                    <div
                        class="w-20 h-20 bg-orange text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-heading font-bold uppercase mb-2 text-orange">Başvurunuz Alındı!</h3>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('#<?php echo esc_attr($id); ?> form');
        const success = document.getElementById('v-form-success-<?php echo esc_attr($block['id']); ?>');

        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = form.querySelector('.submit-btn');
            const btnText = btn.querySelector('.btn-text');

            btn.disabled = true;
            btnText.innerText = 'Gönderiliyor...';

            const formData = new FormData(form);
            formData.append('action', 'iff_submit_form');
            formData.append('form_type', 'volunteer');
            formData.append('nonce', iff_ajax.nonce);

            fetch(iff_ajax.url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        form.classList.add('hidden');
                        success.classList.remove('hidden');
                    } else {
                        alert('Bir hata oluştu, lütfen tekrar deneyin.');
                        btn.disabled = false;
                        btnText.innerText = 'Başvuruyu Tamamla';
                    }
                });
        });
    });
</script>

<style>
    .v-input::placeholder {
        font-size: 14px;
        color: #e2e8f0;
        letter-spacing: 0.05em;
    }

    .volunteer-form-block .modern-shadow {
        box-shadow: 20px 20px 0px 0px rgba(249, 115, 22, 0.15);
    }

    @media (max-width: 768px) {
        .volunteer-form-block .modern-shadow {
            box-shadow: 10px 10px 0px 0px rgba(249, 115, 22, 0.15);
        }
    }
</style>