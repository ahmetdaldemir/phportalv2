import React from 'react'
import { StyleSheet, Text, View } from 'react-native'
import PrimaryButton from './PrimaryButton'

export type SerialItem = {
  id: number
  stock_id?: number
  stock_name: string
  serial_number: string
  barcode?: string
  color_name?: string
  brand_name?: string
  model_name?: string
  sale_price?: number
  cost_price?: number
  base_cost_price?: number
  seller_name?: string
  status?: string
}

type Props = {
  item: SerialItem
  onEditPrice: (item: SerialItem) => void
  onTransfer: (item: SerialItem) => void
}

const SerialCard: React.FC<Props> = ({ item, onEditPrice, onTransfer }) => (
  <View style={styles.card}>
    <View style={styles.header}>
      <Text style={styles.title}>{item.stock_name}</Text>
      <Text style={styles.serial}>Seri: {item.serial_number}</Text>
      {item.barcode ? <Text style={styles.serial}>Barkod: {item.barcode}</Text> : null}
    </View>
    <View style={styles.meta}>
      <Text style={styles.metaText}>Marka: {item.brand_name || '-'}</Text>
      <Text style={styles.metaText}>Model: {item.model_name || '-'}</Text>
      <Text style={styles.metaText}>Renk: {item.color_name || '-'}</Text>
    </View>
    <View style={styles.priceRow}>
      <View>
        <Text style={styles.label}>Satış</Text>
        <Text style={styles.price}>{formatPrice(item.sale_price)}</Text>
      </View>
      <View>
        <Text style={styles.label}>Maliyet</Text>
        <Text style={styles.priceMuted}>{formatPrice(item.cost_price)}</Text>
      </View>
      <View>
        <Text style={styles.label}>Net</Text>
        <Text style={styles.priceMuted}>{formatPrice(item.base_cost_price)}</Text>
      </View>
    </View>
    <View style={styles.actions}>
      <PrimaryButton title="Fiyat Güncelle" onPress={() => onEditPrice(item)} />
      <PrimaryButton
        title="Sevk Et"
        onPress={() => onTransfer(item)}
        style={{ backgroundColor: '#0f766e' }}
      />
    </View>
  </View>
)

const formatPrice = (value?: number) =>
  typeof value === 'number' ? `${value.toLocaleString('tr-TR')} ₺` : '—'

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    padding: 16,
    borderRadius: 16,
    shadowColor: '#0f172a',
    shadowOpacity: 0.06,
    shadowOffset: { width: 0, height: 8 },
    shadowRadius: 16,
    elevation: 3,
    gap: 12
  },
  header: {
    gap: 4
  },
  title: {
    fontSize: 18,
    fontWeight: '600',
    color: '#111827'
  },
  serial: {
    fontSize: 14,
    color: '#4b5563'
  },
  meta: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12
  },
  metaText: {
    fontSize: 13,
    color: '#6b7280'
  },
  priceRow: {
    flexDirection: 'row',
    justifyContent: 'space-between'
  },
  label: {
    fontSize: 12,
    color: '#9ca3af',
    marginBottom: 4
  },
  price: {
    fontSize: 16,
    fontWeight: '700',
    color: '#2563eb'
  },
  priceMuted: {
    fontSize: 16,
    fontWeight: '600',
    color: '#475569'
  },
  actions: {
    flexDirection: 'row',
    gap: 12
  }
})

export default SerialCard

