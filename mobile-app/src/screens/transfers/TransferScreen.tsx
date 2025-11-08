import React, { useEffect, useState } from 'react'
import { Alert, FlatList, StyleSheet, Text, TextInput, View } from 'react-native'
import ScreenContainer from '@components/ScreenContainer'
import PrimaryButton from '@components/PrimaryButton'
import { useApi } from '@hooks/useApi'

type TransferItem = {
  id: number
  serial_number: string
  stock_name: string
  seller_name?: string
  status: string
  created_at: string
}

const TransferScreen = () => {
  const api = useApi()
  const [serialInput, setSerialInput] = useState('')
  const [description, setDescription] = useState('')
  const [transfers, setTransfers] = useState<TransferItem[]>([])
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    loadTransfers()
  }, [])

  const loadTransfers = async () => {
    try {
      const response = await api.get('/transfer/list', { params: { per_page: 20 } })
      setTransfers(response.data?.data ?? response.data ?? [])
    } catch (error: any) {
      Alert.alert('Hata', error?.response?.data?.message ?? 'Sevk kayıtları alınamadı.')
    }
  }

  const handleCreateTransfer = async () => {
    if (!serialInput.trim()) {
      Alert.alert('Uyarı', 'Lütfen sevk edilecek seri numarasını giriniz.')
      return
    }

    try {
      setLoading(true)
      await api.post('/transfer/store', {
        serial_number: serialInput.trim(),
        description
      })
      Alert.alert('Başarılı', 'Sevk kaydı oluşturuldu.')
      setSerialInput('')
      setDescription('')
      loadTransfers()
    } catch (error: any) {
      Alert.alert('Hata', error?.response?.data?.message ?? 'Sevk oluşturulamadı.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <ScreenContainer scrollable>
      <View style={styles.form}>
        <Text style={styles.label}>Seri Numarası / Barkod</Text>
        <TextInput
          value={serialInput}
          onChangeText={setSerialInput}
          placeholder="Seri ya da barkod girin"
          style={styles.input}
          autoCapitalize="characters"
          autoCorrect={false}
        />

        <Text style={styles.label}>Açıklama (opsiyonel)</Text>
        <TextInput
          value={description}
          onChangeText={setDescription}
          placeholder="Sevk notu"
          style={[styles.input, { minHeight: 80 }]}
          multiline
        />

        <PrimaryButton title="Sevk Oluştur" onPress={handleCreateTransfer} disabled={loading} />
      </View>

      <Text style={styles.sectionTitle}>Son Sevk İşlemleri</Text>
      <FlatList
        data={transfers}
        keyExtractor={item => String(item.id)}
        renderItem={({ item }) => (
          <View style={styles.transferCard}>
            <View style={{ flex: 1, gap: 4 }}>
              <Text style={styles.transferTitle}>{item.stock_name}</Text>
              <Text style={styles.transferMeta}>Seri: {item.serial_number}</Text>
              <Text style={styles.transferMeta}>Şube: {item.seller_name ?? '-'}</Text>
            </View>
            <View style={{ gap: 4, alignItems: 'flex-end' }}>
              <Text style={styles.transferStatus}>{item.status}</Text>
              <Text style={styles.transferDate}>{item.created_at}</Text>
            </View>
          </View>
        )}
        ItemSeparatorComponent={() => <View style={{ height: 12 }} />}
      />
    </ScreenContainer>
  )
}

const styles = StyleSheet.create({
  form: {
    gap: 12,
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 16,
    shadowColor: '#0f172a',
    shadowOpacity: 0.04,
    shadowOffset: { width: 0, height: 10 },
    shadowRadius: 20,
    elevation: 2
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1f2937'
  },
  input: {
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    paddingHorizontal: 14,
    paddingVertical: 12,
    fontSize: 16
  },
  sectionTitle: {
    marginTop: 24,
    marginBottom: 12,
    fontSize: 18,
    fontWeight: '700',
    color: '#111827'
  },
  transferCard: {
    flexDirection: 'row',
    padding: 16,
    backgroundColor: '#fff',
    borderRadius: 16,
    shadowColor: '#0f172a',
    shadowOpacity: 0.05,
    shadowOffset: { width: 0, height: 8 },
    shadowRadius: 16,
    elevation: 2
  },
  transferTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#0f172a'
  },
  transferMeta: {
    fontSize: 13,
    color: '#64748b'
  },
  transferStatus: {
    fontSize: 13,
    fontWeight: '600',
    color: '#2563eb'
  },
  transferDate: {
    fontSize: 12,
    color: '#9ca3af'
  }
})

export default TransferScreen

