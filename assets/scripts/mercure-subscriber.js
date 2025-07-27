export class MercureSubscriber {
    constructor({ topic, tokenUrl, hubUrl, refreshIntervalMinutes = 55, onMessage }) {
        this.topic = topic;
        this.tokenUrl = tokenUrl;
        this.hubUrl = hubUrl;
        this.refreshInterval = refreshIntervalMinutes * 60 * 1000;
        this.onMessage = onMessage;

        this.eventSource = null;
        this.tokenRefreshTimer = null;
    }

    async fetchToken() {
        try {
            const response = await fetch(this.tokenUrl, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            return data.token;
        } catch (error) {
            console.error('Ошибка при получении Mercure токена:', error);
            return null;
        }
    }

    async connect() {
        const token = await this.fetchToken();
        if (!token) return;

        const url = new URL(this.hubUrl);
        url.searchParams.append('topic', this.topic);

        if (this.eventSource) {
            this.eventSource.close();
        }

        document.cookie = "mercureAuthorization=" + token + "; path=/.well-known/mercure;";

        this.eventSource = new EventSource(url.toString(), {
            withCredentials: true,
        });

        this.eventSource.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                if (this.onMessage) {
                    this.onMessage(data);
                }
            } catch (err) {
                console.warn('Неверный JSON в событии Mercure:', err);
            }
        };

        this.eventSource.onerror = (error) => {
            console.warn('Mercure connection error:', error);
        };

        this.tokenRefreshTimer = setTimeout(() => {
            this.connect(); // reconnect with new token
        }, this.refreshInterval);
    }

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
        }
        if (this.tokenRefreshTimer) {
            clearTimeout(this.tokenRefreshTimer);
        }
    }
}
