// Kayan yazı
let text = "Sigara kullanımı COPD ve depresyon riskini artırır.";
let pos = 0;

function slideText() {
    let area = document.getElementById("slidingText");
    area.innerHTML = text.substring(pos) + text.substring(0, pos);
    pos++;
    if (pos > text.length) pos = 0;
}

setInterval(slideText, 150);

// Kayan bilgilendirme yazısı
const messages = [
  "Sigara kullanımı KOAH riskini önemli ölçüde artırır.",
  "Yetersiz uyku ruh sağlığını olumsuz etkiler.",
  "Düzenli fiziksel aktivite riski azaltır.",
  "Erken risk tespiti hayat kurtarır."
];

let index = 0;
const infoBox = document.getElementById("slidingText");

if (infoBox) {
  setInterval(() => {
    infoBox.textContent = messages[index];
    index = (index + 1) % messages.length;
  }, 3000);
}
