import React, { useCallback, useEffect, useState } from 'react'
import { Alert, FlatList, RefreshControl, StyleSheet, View, Text, TextInput } from 'react-native'
import ScreenContainer from '@components/ScreenContainer'
import SerialCard, { SerialItem } from '@components/SerialCard'
import PrimaryButton from '@components/PrimaryButton'
import { useApi } from '@hooks/useApi'
import LoadingOverlay from '@components/LoadingOverlay'

type SerialResponseItem = {
  id: number
  serial_number: string
  barcode?: string
  sale_price?: number
  cost_price?: number
  base_cost_price?: number
  stock: {
    id: number
    name: string
    brand?: { name: string }
    versionNames?: () => string
  }
  color?: { name: string }
  seller?: { name: string }
}

const SerialListScreen = () => {
  const api = useApi()
  const [serials, setSerials] = useState<SerialItem[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [selected, setSelected] = useState<SerialItem | null>(null)
  const [priceLoading, setPriceLoading] = useState(false)

  const loadSerials = useCallback(async () => {
    try {
      setLoading(true)
      const response = await api.get('/stockcard/serialList', {
        params: { per_page: 20 }
      })

      const items: SerialItem[] = (response.data?.data ?? response.data ?? []).map(
        (item: SerialResponseItem) => ({
          id: item.id,
          stock_id: item.stock?.id,
          serial_number: item.serial_number,
          barcode: item.barcode,
          stock_name: item.stock?.name ?? 'Stok',
          brand_name: item.stock?.brand?.name,
          model_name: extractVersions(item),
          color_name: item.color?.name,
          sale_price: item.sale_price,
          cost_price: item.cost_price,
          base_cost_price: item.base_cost_price,
          seller_name: item.seller?.name
        })
      )

      setSerials(items)
    } catch (error: any) {
      Alert.alert('Hata', error?.response?.data?.message ?? 'Seri numaraları alınamadı')
    } finally {
      setLoading(false)
    }
  }, [api])

  useEffect(() => {
    loadSerials()
  }, [loadSerials])

  const onRefresh = async () => {
    setRefreshing(true)
    await loadSerials()
    setRefreshing(false)
  }

  const handleEditPrice = (item: SerialItem) => {
    setSelected(item)
  }

  const handlePriceSubmit = async (priceData: { cost_price: number; base_cost_price: number; sale_price: number }) => {
    if (!selected) return
    try {
      setPriceLoading(true)
      await api.post('/stockcard/singlepriceupdate', {
        id: selected.id,
        cost_price: priceData.cost_price,
        base_cost_price: priceData.base_cost_price,
        sale_price: priceData.sale_price
      })
      Alert.alert('Başarılı', 'Fiyatlar güncellendi.')
      setSelected(null)
      loadSerials()
    } catch (error: any) {
      Alert.alert('Hata', error?.response?.data ?? 'Fiyat güncellenemedi')
    } finally {
      setPriceLoading(false)
    }
  }

  const handleTransfer = async (item: SerialItem) => {
    try {
      await api.post('/transfer/store', {
        serial_number: item.serial_number,
        stock_id: item.stock_id
      })
      Alert.alert('Sevk kaydedildi', 'Sevk işlemi oluşturuldu.')
    } catch (error: any) {
      Alert.alert('Hata', error?.response?.data?.message ?? 'Sevk işlemi başarısız.')
    }
  }

  if (loading && !refreshing) {
    return <LoadingOverlay message="Seri numaraları yükleniyor..." />
  }

  return (
    <ScreenContainer>
      <View style={styles.actions}>
        <PrimaryButton title="Yenile" onPress={loadSerials} />
      </View>
      <FlatList
        data={serials}
        keyExtractor={item => String(item.id)}
        renderItem={({ item }) => (
          <SerialCard item={item} onEditPrice={handleEditPrice} onTransfer={handleTransfer} />
        )}
        contentContainerStyle={{ gap: 16, paddingBottom: 24 }}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={<PrimaryButton title="Kayıt bulunamadı - Yenile" onPress={loadSerials} />}
      />

      {selected ? (
        <PriceModal
          item={selected}
          onClose={() => setSelected(null)}
          onSubmit={handlePriceSubmit}
          loading={priceLoading}
        />
      ) : null}
    </ScreenContainer>
  )
}

const extractVersions = (item: SerialResponseItem) => {
  try {
    const versionNames = (item as any).stock?.version_names ?? []
    if (Array.isArray(versionNames) && versionNames.length > 0) return versionNames.join(', ')
    const fn = item.stock?.versionNames
    if (typeof fn === 'function') {
      const json = fn()
      const parsed = JSON.parse(json)
      if (Array.isArray(parsed)) return parsed.join(', ')
    }
  } catch {
    // ignore
  }
  return undefined
}

type PricePayload = { cost_price: number; base_cost_price: number; sale_price: number }

const PriceModal: React.FC<{
  item: SerialItem
  loading: boolean
  onClose: () => void
  onSubmit: (payload: PricePayload) => void
}> = ({ item, onClose, onSubmit, loading }) => {
  const [costPrice, setCostPrice] = useState(String(item.cost_price ?? ''))
  const [basePrice, setBasePrice] = useState(String(item.base_cost_price ?? ''))
  const [salePrice, setSalePrice] = useState(String(item.sale_price ?? ''))

  return (
    <View style={styles.modalOverlay}>
      <View style={styles.modalContent}>
        <Text style={styles.modalTitle}>{item.stock_name}</Text>
        <View style={styles.modalBody}>
          <PriceInput label="Maliyet" value={costPrice} onChangeText={setCostPrice} />
          <PriceInput label="Net Maliyet" value={basePrice} onChangeText={setBasePrice} />
          <PriceInput label="Satış" value={salePrice} onChangeText={setSalePrice} />
        </View>
        <View style={styles.modalActions}>
          <PrimaryButton title="İptal" onPress={onClose} style={{ backgroundColor: '#9ca3af' }} />
          <PrimaryButton
            title="Kaydet"
            onPress={() =>
              onSubmit({
                cost_price: Number(costPrice),
                base_cost_price: Number(basePrice),
                sale_price: Number(salePrice)
              })
            }
            disabled={loading}
          />
        </View>
      </View>
    </View>
  )
}

const PriceInput: React.FC<{ label: string; value: string; onChangeText: (text: string) => void }> = ({
  label,
  value,
  onChangeText
}) => (
  <View style={{ gap: 6 }}>
    <Text style={{ fontSize: 14, fontWeight: '600', color: '#1f2937' }}>{label}</Text>
    <View style={styles.modalInputWrapper}>
      <TextInput
        value={value}
        onChangeText={onChangeText}
        keyboardType="decimal-pad"
        style={styles.modalTextInput}
      />
    </View>
  </View>
)

const styles = StyleSheet.create({
  actions: {
    flexDirection: 'row',
    justifyContent: 'flex-end'
  },
  modalOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(15, 23, 42, 0.35)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 16
  },
  modalContent: {
    backgroundColor: '#fff',
    borderRadius: 16,
    width: '100%',
    padding: 20,
    gap: 16
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: '#111827'
  },
  modalBody: {
    gap: 12
  },
  modalInputWrapper: {
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#e2e8f0'
  },
  modalTextInput: {
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 16,
    color: '#0f172a'
  },
  modalActions: {
    flexDirection: 'row',
    gap: 12,
    justifyContent: 'flex-end'
  }
})

export default SerialListScreen

