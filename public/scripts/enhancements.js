/* ===============================================
   Enhancement 1: Product Comparison Panel
   & Enhancement 2: Room Size Audio Planner
   Author: Bikram Bhattarai (EchoSphere)
   =============================================== */
"use strict";

document.addEventListener("DOMContentLoaded", () => {
  /* --------- Enhancement 1: Product Comparison --------- */
  (function () {
    const panel = document.getElementById("comparison-panel");
    const content = document.getElementById("comparison-content");
    const closeBtn = document.getElementById("close-comparison");
    if (!panel || !content || !closeBtn) return;           // only on product.html

    const selected = new Set(); // stores actual .product sections

    // Auto-inject "Compare" buttons and wire up
    document.querySelectorAll(".product").forEach(section => {
      const h2 = section.querySelector("h2"); if (!h2) return;
      let btn = section.querySelector(".compare-btn");
      if (!btn) {
        btn = document.createElement("button");
        btn.type = "button";
        btn.className = "compare-btn";
        btn.textContent = "Compare";
        h2.insertAdjacentElement("afterend", btn);
      }
      btn.addEventListener("click", () => {
        if (selected.has(section)) { selected.delete(section); btn.textContent = "Compare"; }
        else if (selected.size < 3) { selected.add(section); btn.textContent = "Remove"; }
        else { alert("You can compare up to 3 products."); }
        buildPanel();
      });
    });

    closeBtn.addEventListener("click", () => {
      selected.clear();
      content.innerHTML = "";
      panel.style.display = "none";
      document.querySelectorAll(".compare-btn").forEach(b => b.textContent = "Compare");
    });

    function buildPanel() {
      if (selected.size === 0) { panel.style.display = "none"; return; }
      panel.style.display = "block";
      content.innerHTML = "";
      selected.forEach(section => {
        const title = section.querySelector("h2")?.textContent || "Product";
        const price = (title.match(/\$\d[\d,.]*/) || [""])[0];
        const features = [...section.querySelectorAll(".product-features li")]
          .map(li => `<li>${li.textContent}</li>`).join("");
        content.insertAdjacentHTML("beforeend",
          `<div class="compare-card">
             <h4>${title}</h4>
             <p><strong>Price:</strong> ${price}</p>
             <ul>${features}</ul>
           </div>`);
      });
    }
  })();

  /* --------- Enhancement 2: Room Size Audio Planner --------- */
  (function () {
    // Only runs on enquire.html where these IDs exist
    const roomSel = document.getElementById("room-size");
    const productSel = document.getElementById("product");
    const msg = document.getElementById("planner-reco");
    if (!roomSel || !productSel || !msg) return;

    const plan = {
      small: {
        product: "essence", features: ["smart_hub"],
        text: "Small room → Essence Series recommended 👍"
      },
      medium: {
        product: "nexus", features: ["wireless_rear"],
        text: "Medium room → Nexus Series for balanced immersion 🔊"
      },
      large: {
        product: "aurora", features: ["wireless_rear", "professional_calibration"],
        text: "Large room → Aurora Series for full Atmos experience 🎬"
      }
    };

    roomSel.addEventListener("change", () => {
      const choice = plan[roomSel.value];
      if (!choice) { msg.classList.remove("show"); msg.textContent = ""; return; }

      // 1) Set product based on room size
      productSel.value = choice.product;
      productSel.dispatchEvent(new Event("change", { bubbles: true }));

      // 2) Tick recommended features (keep any others the user already chose)
      document.querySelectorAll('input[name="features"]').forEach(cb => {
        if (choice.features.includes(cb.value)) {
          cb.checked = true;
          cb.dispatchEvent(new Event("change", { bubbles: true }));
        }
      });

      // 3) Show the recommendation message (announced to screen readers via aria-live)
      msg.textContent = choice.text;
      msg.classList.add("show");
    });
  })();
});
