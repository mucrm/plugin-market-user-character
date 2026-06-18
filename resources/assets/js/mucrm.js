document.addEventListener("DOMContentLoaded", () => {
  lucide.createIcons();
});

window.addEventListener("alpine:init", () => {
  Alpine.data("MUCRMSlider", (totalSlides) => ({
    active: 0,
    timer: null,
    total: totalSlides,
    isWaiting: false,

    init() {
      this.startTimer();
      document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
          this.stopTimer();
        } else {
          this.startTimer();
        }
      });
    },

    startTimer() {
      this.stopTimer();
      this.timer = setInterval(() => {
        this.next();
      }, 5000);
    },

    stopTimer() {
      if (this.timer) clearInterval(this.timer);
    },

    handleNav(direction) {
      if (this.isWaiting) return;

      this.isWaiting = true;

      if (direction === 'next') {
        this.active = (this.active + 1) % this.total;
      } else {
        this.active = (this.active - 1 + this.total) % this.total;
      }

      this.startTimer();

      setTimeout(() => {
        this.isWaiting = false;
      }, 600);
    },

    next() {
      this.handleNav('next');
    },

    prev() {
      this.handleNav('prev');
    },

    jump(index) {
      if (this.active === index) return;

      this.isWaiting = true;

      this.active = index;
      this.startTimer();

      setTimeout(() => {
        this.isWaiting = false;
      }, 600);
    },
  }));

  Alpine.data("MUCRMModal", (totalSlides) => ({
    open: false,
    active: 0,
    total: totalSlides,
    timer: null,
    isWaiting: false,
    cooldown: 10 * 60 * 1000,
    storageKey: 'mucrm_modal_last_closed',

    init() {
      if (this.canShow()) {
        setTimeout(() => {
          this.open = true;
          this.startTimer();
        }, 300);
      }
    },

    canShow() {
      const lastClosed = localStorage.getItem(this.storageKey);
      if (!lastClosed) return true;
      const now = new Date().getTime();
      return (now - lastClosed) > this.cooldown;
    },

    closeModal() {
      this.open = false;
      this.stopTimer();
      localStorage.setItem(this.storageKey, new Date().getTime());
    },

    startTimer() {
      this.stopTimer();
      this.timer = setInterval(() => {
        this.next();
      }, 5000);
    },

    stopTimer() {
      if (this.timer) clearInterval(this.timer);
    },

    // Centralizamos a navegação com proteção de 600ms
    handleNav(direction) {
      if (this.isWaiting) return;
      this.isWaiting = true;

      if (direction === 'next') {
        this.active = (this.active + 1) % this.total;
      } else {
        this.active = (this.active - 1 + this.total) % this.total;
      }

      this.startTimer();

      setTimeout(() => {
        this.isWaiting = false;
      }, 600);
    },

    next() {
      this.handleNav('next');
    },

    prev() {
      this.handleNav('prev');
    },

    jump(index) {
      if (this.active === index || this.isWaiting) return;
      this.isWaiting = true;

      this.active = index;
      this.startTimer();

      setTimeout(() => {
        this.isWaiting = false;
      }, 600);
    }
  }));

  Alpine.data("shopItem", (excellentCount, basePrice, taxes, repeatSocket) => ({
    maxExcelents: excellentCount,
    selectedCount: 0,
    totalPrice: basePrice,
    basePrice: basePrice,
    taxes: taxes,
    sockets: [255, 255, 255, 255, 255],
    repeatSocket: repeatSocket,

    checkLimit(e) {
      if (e.target.checked) {
        if (this.selectedCount >= this.maxExcelents) {
          e.target.checked = false;
          return;
        }
        this.selectedCount++;
        this.totalPrice += this.taxes.excellent;
      } else {
        this.selectedCount--;
        this.totalPrice -= this.taxes.excellent;
      }
    },

    changeLevel(e) {
      const newLevel = parseInt(e.target.value);

      const oldLevelCost = this.currentLevel * this.taxes.level;
      this.totalPrice -= oldLevelCost;

      const newLevelCost = newLevel * this.taxes.level;
      this.totalPrice += newLevelCost;

      this.currentLevel = newLevel;
    },

    changeSocket(index, e) {
      const val = parseInt(e.target.value);
      if (
        !this.repeatSocket &&
        val < 250 &&
        this.sockets.some((s, i) => s === val && i !== index)
      ) {
        window.MUCRMAlert({
          title: "Informação",
          text: "Este atributo já foi selecionado em outro slot!",
          type: "info",
          timer: 3000,
        });
        e.target.value = 254; // Reseta para vazio
        this.sockets[index] = 254;
        return;
      }

      this.sockets[index] = val;
    },

    toggleLuck(e) {
      if (e.target.checked) {
        this.totalPrice += this.taxes.luck;
      } else {
        this.totalPrice -= this.taxes.luck;
      }
    },

    toggleSkill(e) {
      if (e.target.checked) {
        this.totalPrice += this.taxes.skill;
      } else {
        this.totalPrice -= this.taxes.skill;
      }
    },

    toggleAncient(tier) {
      if (tier === "ancient_1" && this.ancient_1) {
        if (this.ancient_2) {
          this.ancient_2 = false;
        }
      }

      if (tier === "ancient_2" && this.ancient_2) {
        if (this.ancient_1) {
          this.ancient_1 = false;
        }
      }
    },
  }));

  Alpine.data("deleteConfirm", () => ({
    open: false,
    formToSubmit: null,

    confirm(e) { // Mudei para confirm para bater com o seu HTML
      e.preventDefault();
      // Captura o formulário mais próximo do clique
      this.formToSubmit = e.target.closest("form");
      this.open = true;
    },

    submit() {
      if (this.formToSubmit) {
        this.formToSubmit.submit();
      }
      this.open = false;
    },

    cancel() {
      this.open = false;
      this.formToSubmit = null;
    }
  }));
});
