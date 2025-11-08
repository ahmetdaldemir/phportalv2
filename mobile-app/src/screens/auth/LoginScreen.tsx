import React, { useState } from 'react'
import { Alert, Image, StyleSheet, Text, TextInput, View } from 'react-native'
import ScreenContainer from '@components/ScreenContainer'
import PrimaryButton from '@components/PrimaryButton'
import { useApi } from '@hooks/useApi'
import { useAuthStore } from '@store/useAuthStore'

const LoginScreen = () => {
  const api = useApi()
  const setCredentials = useAuthStore(state => state.setCredentials)
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert('Uyarı', 'Lütfen kullanıcı adı ve şifrenizi giriniz.')
      return
    }

    try {
      setLoading(true)
      const response = await api.post('/login', { email, password })

      if (response.data?.token) {
        await setCredentials(response.data.token, {
          id: response.data.user?.id,
          name: response.data.user?.name,
          roles: response.data.roles ?? []
        })
      } else {
        Alert.alert('Giriş başarısız', 'Yanıt formatı beklenen gibi değil.')
      }
    } catch (error: any) {
      Alert.alert('Giriş başarısız', error?.response?.data?.message ?? 'Lütfen bilgilerinizi kontrol edin.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <ScreenContainer>
      <View style={styles.header}>
        <Image source={{ uri: 'https://phportal.net/img/147836.png' }} style={styles.logo} />
        <Text style={styles.title}>PH Portal Mobil</Text>
        <Text style={styles.subtitle}>Stok & satış işlemlerinize hızlı erişim</Text>
      </View>

      <View style={styles.form}>
        <Text style={styles.label}>Kullanıcı Adı</Text>
        <TextInput
          value={email}
          onChangeText={setEmail}
          placeholder="ornek@phportal.com"
          autoCapitalize="none"
          keyboardType="email-address"
          style={styles.input}
        />

        <Text style={styles.label}>Şifre</Text>
        <TextInput
          value={password}
          onChangeText={setPassword}
          placeholder="••••••••"
          secureTextEntry
          style={styles.input}
        />

        <PrimaryButton title="Giriş Yap" onPress={handleLogin} disabled={loading} />
      </View>
    </ScreenContainer>
  )
}

const styles = StyleSheet.create({
  header: {
    alignItems: 'center',
    gap: 12,
    paddingTop: 48
  },
  logo: {
    width: 72,
    height: 72,
    borderRadius: 16
  },
  title: {
    fontSize: 24,
    fontWeight: '700',
    color: '#111827'
  },
  subtitle: {
    fontSize: 14,
    color: '#6b7280',
    textAlign: 'center'
  },
  form: {
    marginTop: 32,
    gap: 12
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: '#374151'
  },
  input: {
    backgroundColor: '#fff',
    paddingHorizontal: 14,
    paddingVertical: 12,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#e5e7eb'
  }
})

export default LoginScreen

