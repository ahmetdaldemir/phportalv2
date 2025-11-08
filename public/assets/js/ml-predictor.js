/**
 * TensorFlow.js ile Stok Tahmin Modeli
 * Basit bir linear regression modeli kullanarak satış tahminleri yapar
 */

class StockMLPredictor {
    constructor() {
        this.model = null;
        this.isModelTrained = false;
    }

    /**
     * Basit bir Linear Regression modeli oluştur
     */
    createModel() {
        const model = tf.sequential();
        
        // Input layer (4 özellik: avg_days_to_sell, current_stock, last_7_days, last_30_days)
        model.add(tf.layers.dense({
            units: 16,
            activation: 'relu',
            inputShape: [4]
        }));
        
        // Hidden layer
        model.add(tf.layers.dense({
            units: 8,
            activation: 'relu'
        }));
        
        // Output layer (1 çıktı: gelecek hafta satış tahmini)
        model.add(tf.layers.dense({
            units: 1,
            activation: 'linear'
        }));
        
        model.compile({
            optimizer: tf.train.adam(0.01),
            loss: 'meanSquaredError',
            metrics: ['mae']
        });
        
        return model;
    }

    /**
     * Veriyi normalize et
     */
    normalizeData(data, min, max) {
        return data.map(val => (val - min) / (max - min + 1e-7));
    }

    /**
     * Stok verisi ile modeli eğit
     */
    async trainModel(stockData) {
        try {
            console.log('🤖 ML Model eğitimi başlıyor...');
            
            if (!stockData || stockData.length < 10) {
                console.warn('Yeterli veri yok, model eğitilemedi');
                return false;
            }

            // Özellikler ve hedef değer
            const features = [];
            const labels = [];

            stockData.forEach(item => {
                // Features: [avg_days_to_sell, current_stock, last_7_days_sales, last_30_days_sales]
                features.push([
                    item.avg_days_to_sell || 0,
                    item.current_stock || 0,
                    item.last_7_days_sales || 0,
                    item.last_30_days_sales || 0
                ]);
                
                // Label: next week prediction (basit hesaplama)
                const dailyRate = (item.total_sold || 0) / 90;
                const trend = item.last_7_days_sales / Math.max(item.last_30_days_sales / 4, 1);
                labels.push([dailyRate * 7 * trend]);
            });

            // Tensörlere çevir
            const xs = tf.tensor2d(features);
            const ys = tf.tensor2d(labels);

            // Modeli oluştur
            this.model = this.createModel();

            // Eğit
            await this.model.fit(xs, ys, {
                epochs: 50,
                batchSize: 32,
                validationSplit: 0.2,
                verbose: 0,
                callbacks: {
                    onEpochEnd: (epoch, logs) => {
                        if (epoch % 10 === 0) {
                            console.log(`Epoch ${epoch}: loss = ${logs.loss.toFixed(4)}`);
                        }
                    }
                }
            });

            // Cleanup
            xs.dispose();
            ys.dispose();

            this.isModelTrained = true;
            console.log('✅ ML Model eğitimi tamamlandı!');
            return true;

        } catch (error) {
            console.error('ML Model eğitim hatası:', error);
            return false;
        }
    }

    /**
     * Tahmin yap
     */
    predict(stockItem) {
        if (!this.isModelTrained || !this.model) {
            // Model eğitilmemişse basit hesaplama
            const dailyRate = (stockItem.total_sold || 0) / 90;
            const trend = stockItem.last_7_days_sales / Math.max(stockItem.last_30_days_sales / 4, 1);
            return Math.round(dailyRate * 7 * trend);
        }

        try {
            const features = tf.tensor2d([[
                stockItem.avg_days_to_sell || 0,
                stockItem.current_stock || 0,
                stockItem.last_7_days_sales || 0,
                stockItem.last_30_days_sales || 0
            ]]);

            const prediction = this.model.predict(features);
            const value = prediction.dataSync()[0];

            // Cleanup
            features.dispose();
            prediction.dispose();

            return Math.max(0, Math.round(value));

        } catch (error) {
            console.error('Tahmin hatası:', error);
            const dailyRate = (stockItem.total_sold || 0) / 90;
            return Math.round(dailyRate * 7);
        }
    }

    /**
     * Modeli temizle
     */
    dispose() {
        if (this.model) {
            this.model.dispose();
            this.model = null;
            this.isModelTrained = false;
        }
    }

    /**
     * Anomali skorunu hesapla (0-1 arası)
     */
    calculateAnomalyScore(stockItem, allStockData) {
        if (!allStockData || allStockData.length === 0) return 0;

        try {
            // Z-score bazlı anomali tespiti
            const values = allStockData.map(item => item.avg_days_to_sell || 0);
            const mean = values.reduce((a, b) => a + b, 0) / values.length;
            const variance = values.reduce((a, b) => a + Math.pow(b - mean, 2), 0) / values.length;
            const stdDev = Math.sqrt(variance);

            const zScore = Math.abs((stockItem.avg_days_to_sell - mean) / (stdDev + 1e-7));
            
            // 0-1 arası normalize (z-score > 3 = anomali)
            return Math.min(1, zScore / 3);

        } catch (error) {
            console.error('Anomali skoru hesaplama hatası:', error);
            return 0;
        }
    }

    /**
     * Clustering için basit K-means (3 küme: fast, medium, slow movers)
     */
    clusterStocks(stockData) {
        if (!stockData || stockData.length < 3) return [];

        try {
            // Devir hızına göre sırala
            const sorted = [...stockData].sort((a, b) => 
                (a.avg_days_to_sell || 0) - (b.avg_days_to_sell || 0)
            );

            const fastThreshold = Math.percentile(sorted.map(s => s.avg_days_to_sell), 33);
            const slowThreshold = Math.percentile(sorted.map(s => s.avg_days_to_sell), 66);

            return stockData.map(item => {
                const rate = item.avg_days_to_sell || 0;
                if (rate <= fastThreshold) return { ...item, cluster: 'fast' };
                if (rate >= slowThreshold) return { ...item, cluster: 'slow' };
                return { ...item, cluster: 'medium' };
            });

        } catch (error) {
            console.error('Clustering hatası:', error);
            return stockData;
        }
    }
}

// Helper: Percentile hesapla
Math.percentile = function(arr, p) {
    if (arr.length === 0) return 0;
    const sorted = [...arr].sort((a, b) => a - b);
    const index = Math.ceil((p / 100) * sorted.length) - 1;
    return sorted[Math.max(0, index)];
};

// Global instance
window.stockMLPredictor = new StockMLPredictor();

