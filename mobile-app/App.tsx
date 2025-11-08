import { StatusBar } from 'expo-status-bar'
import React from 'react'
import { NavigationContainer } from '@react-navigation/native'
import { SafeAreaProvider } from 'react-native-safe-area-context'
import RootNavigator from '@navigation/RootNavigator'
import { ApiProvider } from '@hooks/useApi'
import useAuthBootstrap from '@hooks/useAuthBootstrap'
import LoadingOverlay from '@components/LoadingOverlay'

export default function App() {
  const { isBootstrapping } = useAuthBootstrap()

  return (
    <SafeAreaProvider>
      <ApiProvider>
        <NavigationContainer>
          {isBootstrapping ? <LoadingOverlay message="Uygulama hazırlanıyor..." /> : <RootNavigator />}
        </NavigationContainer>
      </ApiProvider>
      <StatusBar style="auto" />
    </SafeAreaProvider>
  )
}

