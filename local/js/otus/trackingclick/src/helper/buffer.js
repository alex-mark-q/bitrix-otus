export class ClickBuffer {
  constructor() {
    this.buffer = [];
    this.maxSize = 4;
    this.loadPendingClicks(); // Загружаем неотправленные клики из localStorage
  }

  addClick(eventData) {
    console.log({
      ...eventData,
      timestamp: new Date().toISOString(),
      pageUrl: window.location.href,
      userId: this.getUserId(),
      sessionId: this.getSessionId(),
      userAgent: navigator.userAgent.substring(0, 500),
    });
    this.buffer.push({
      ...eventData,
      timestamp: new Date().toISOString(),
      pageUrl: window.location.href,
      userId: this.getUserId(),
      sessionId: this.getSessionId(),
      userAgent: navigator.userAgent.substring(0, 500),
    });

    if (this.buffer.length >= this.maxSize) {
      this.flush();
    }
  }
  getUserId() {
    try {
      return BX.message('USER_ID') || 0;
    } catch (e) {
      return 0;
    }
  }

  // Получение ID сессии Bitrix
  getSessionId() {
    try {
      return BX.bitrix_sessid() || '';
    } catch (e) {
      return '';
    }
  }

  async flush() {

    if (this.buffer.length === 0) return;

    const clicksToSend = [...this.buffer];
    this.buffer = [];

    console.log("flush", clicksToSend);

    try {
      if (!navigator.onLine) {
        throw new Error('Offline mode');
      }

      await this.sendToDB(clicksToSend);
      this.clearPendingClicks(); // Очищаем сохраненные клики после успешной отправки
    } catch (error) {
      console.error('Ошибка отправки:', error);
      this.savePendingClicks(clicksToSend);
    }
  }

  async sendToDB(clicks) {
    if (!clicks || clicks.length === 0) {
      throw new Error('Empty click data');
    }

    const endpoint = 'https://cp36741.tw1.ru/rest/1/90bsb0u7ffk5ovk0/otus.originalcontactsdata.add';

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ data: clicks })
      });

      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
      return await response.json();

    } catch (error) {
      console.error('REST API Error:', error);
      throw error;
    }
  }

  savePendingClicks(clicks) {
    const pending = JSON.parse(localStorage.getItem('pendingClicks') || '[]');
    localStorage.setItem('pendingClicks', JSON.stringify([...pending, ...clicks]));
  }

  loadPendingClicks() {
    const pending = JSON.parse(localStorage.getItem('pendingClicks') || '[]');
    if (pending.length > 0) {
      this.buffer = [...pending];
      this.flush(); // Пробуем отправить при загрузке
    }
  }

  clearPendingClicks() {
    localStorage.removeItem('pendingClicks');
  }
}
