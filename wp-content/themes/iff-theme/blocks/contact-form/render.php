<?php
/**
 * Contact Form Block Template.
 */

$form_title = get_field('form_basligi') ?: 'Bize Ulaşın';
$form_description = get_field('form_aciklamasi') ?: 'Sorularınız, önerileriniz veya işbirliği talepleriniz için aşağıdaki formu kullanabilirsiniz.';

$id = 'contact-form-' . $block['id'];
$className = 'contact-form-block';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
?>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?> py-12">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white p-8 md:p-16 border-4 border-warmgray modern-shadow relative overflow-hidden">
            <!-- Dekoratif Arka Plan Elemanı -->
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-red opacity-5 rounded-full"></div>
            <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-orange opacity-10 rounded-full"></div>

            <div class="relative z-10">
                <div class="mb-12">
                    <h2 class="text-4xl md:text-5xl font-heading font-bold uppercase tracking-tighter text-warmgray mb-4">
                        <?php echo esc_html($form_title); ?>
                    </h2>
                    <p class="text-lg text-gray-600 font-serif leading-relaxed max-w-2xl">
                        <?php echo esc_html($form_description); ?>
                    </p>
                </div>

                <form action="#" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-widest text-red">Ad Soyad</label>
                        <input type="text" id="name" name="name" required 
                               class="w-full border-b-2 border-gray-200 focus:border-red outline-none py-3 text-lg transition-colors bg-transparent"
                               placeholder="İsminizi buraya yazın...">
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="block text-xs font-bold uppercase tracking-widest text-red">E-Posta</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full border-b-2 border-gray-200 focus:border-red outline-none py-3 text-lg transition-colors bg-transparent"
                               placeholder="eposta@adresiniz.com">
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label for="subject" class="block text-xs font-bold uppercase tracking-widest text-red">Konu</label>
                        <input type="text" id="subject" name="subject" required 
                               class="w-full border-b-2 border-gray-200 focus:border-red outline-none py-3 text-lg transition-colors bg-transparent"
                               placeholder="Mesajınızın konusu nedir?">
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label for="message" class="block text-xs font-bold uppercase tracking-widest text-red">Mesajınız</label>
                        <textarea id="message" name="message" rows="4" required 
                                  class="w-full border-b-2 border-gray-200 focus:border-red outline-none py-3 text-lg transition-colors bg-transparent resize-none"
                                  placeholder="Bize ne anlatmak istersiniz?"></textarea>
                    </div>

                    <div class="md:col-span-2 pt-8">
                        <button type="submit" 
                                class="submit-btn w-full md:w-auto bg-red text-white px-12 py-5 font-heading font-bold uppercase tracking-widest hover:bg-warmgray transition-all duration-300 transform hover:-translate-y-1 modern-shadow">
                            <span class="btn-text">Gönder</span>
                        </button>
                    </div>
                </form>

                <div id="form-success-<?php echo esc_attr($block['id']); ?>" class="hidden py-20 text-center animate-fade-in">
                    <div class="w-20 h-20 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-heading font-bold uppercase mb-2">Mesajınız Alındı!</h3>
                    <p class="text-gray-600 font-serif">En kısa sürede sizinle iletişime geçeceğiz.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#<?php echo esc_attr($id); ?> form');
    const success = document.getElementById('form-success-<?php echo esc_attr($block['id']); ?>');
    
    if(!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = form.querySelector('.submit-btn');
        const btnText = btn.querySelector('.btn-text');
        
        btn.disabled = true;
        btnText.innerText = 'Gönderiliyor...';

        const formData = new FormData(form);
        formData.append('action', 'iff_submit_form');
        formData.append('form_type', 'contact');
        formData.append('nonce', iff_ajax.nonce);

        fetch(iff_ajax.url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                form.classList.add('hidden');
                success.classList.remove('hidden');
            } else {
                alert('Bir hata oluştu, lütfen tekrar deneyin.');
                btn.disabled = false;
                btnText.innerText = 'Gönder';
            }
        });
    });
});
</script>

<style>
    .modern-shadow {
        box-shadow: 10px 10px 0px 0px rgba(45, 45, 45, 1);
    }
    .contact-form-block input::placeholder, 
    .contact-form-block textarea::placeholder {
        color: #cbd5e1;
        font-style: italic;
        font-size: 0.9em;
    }
    .contact-form-block input:focus::placeholder, 
    .contact-form-block textarea:focus::placeholder {
        opacity: 0.5;
    }
</style>
