import React, { useEffect, useState } from 'react'
import { Alert, StyleSheet, Text, View } from 'react-native'
import { BarCodeScanner } from 'expo-barcode-scanner'
import ScreenContainer from '@components/ScreenContainer'
import PrimaryButton from '@components/PrimaryButton'
import { useApi } from '@hooks/useApi'

const QRSaleScreen = () => {
  const api = useApi()
  const [hasPermission, setHasPermission] = useState<boolean | null>(null)
  const [scannedValue, setScannedValue] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    BarCodeScanner.requestPermissionsAsync().then(({ status }) => {
      setHasPermission(status === 'granted')
    })
  }, [])

  const handleBarCodeScanned = ({ data }: { data: string }) => {
    setScannedValue(data)
    Alert.alert('QR Okundu', data, [
      {
        text: 'Satışa Git',
        onPress: () => initiateSaleFromPayload(data)
      },
      { text: 'Kapat', style: 'cancel' }
    ])
  }

  const initiateSaleFromPayload = async (payload: string) => {
    try {
      setLoading(true)
      const url = new URL(payload)
      const serial = url.searchParams.get('sale_serial')
      const stockId = url.searchParams.get('sale_stock')

      if (!serial) {
        Alert.alert('Arama yapılamadı', 'QR kod geçerli bir seri bilgisi taşımıyor.')
        return
      }

      const response = await api.get('/api/stock/check', {
        params: {
          search: serial
        }
      })

      if (response.data?.success && response.data.exists) {
        Alert.alert('Satışa yönlendiriliyor', 'Seri bulundu, satış ekranına geçebilirsiniz.')
        // native app cannot open internal route so we show info
      } else {
        Alert.alert('Stok bulunamadı', 'Okunan seri sistemde mevcut değil.')
      }
    } catch (error: any) {
      Alert.alert('Hata', error?.response?.data?.message ?? 'Satış sorgusu başarısız.')
    } finally {
      setLoading(false)
    }
  }

  if (hasPermission === null) {
    return (
      <ScreenContainer>
        <Text>QR erişim izni sorgulanıyor...</Text>
      </ScreenContainer>
    )
  }

  if (hasPermission === false) {
    return (
      <ScreenContainer>
        <Text>QR kod okuyucu için kamera izni gerekiyor.</Text>
      </ScreenContainer>
    )
  }

  return (
    <ScreenContainer>
      <View style={styles.scannerWrapper}>
        <BarCodeScanner
          onBarCodeScanned={scannedValue ? undefined : handleBarCodeScanned}
          style={StyleSheet.absoluteFillObject}
        />
      </View>
      <PrimaryButton title="Yeniden Tara" onPress={() => setScannedValue(null)} />
      {loading ? <Text style={{ textAlign: 'center' }}>İşlem yapılıyor...</Text> : null}
    </ScreenContainer>
  )
}

const styles = StyleSheet.create({
  scannerWrapper: {
    height: 320,
    borderRadius: 16,
    overflow: 'hidden',
    backgroundColor: '#000'
  }
})

export default QRSaleScreen

