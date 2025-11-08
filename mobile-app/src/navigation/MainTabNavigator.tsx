import React from 'react'
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs'
import { Ionicons } from '@expo/vector-icons'
import SerialListScreen from '@screens/serials/SerialListScreen'
import TransferScreen from '@screens/transfers/TransferScreen'
import QRSaleScreen from '@screens/sales/QRSaleScreen'
import { Pressable } from 'react-native'
import { useAuthStore } from '@store/useAuthStore'

export type MainTabParamList = {
  Serials: undefined
  Transfer: undefined
  QRSale: undefined
}

const Tab = createBottomTabNavigator<MainTabParamList>()

const MainTabNavigator = () => {
  const logout = useAuthStore(state => state.logout)

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerRight: () => (
          <Pressable onPress={logout} style={{ paddingHorizontal: 16 }}>
            <Ionicons name="log-out-outline" size={20} color="#111" />
          </Pressable>
        ),
        tabBarIcon: ({ color, size }) => {
          switch (route.name) {
            case 'Serials':
              return <Ionicons name="list-outline" size={size} color={color} />
            case 'Transfer':
              return <Ionicons name="swap-horizontal-outline" size={size} color={color} />
            case 'QRSale':
              return <Ionicons name="qr-code-outline" size={size} color={color} />
            default:
              return null
          }
        },
        tabBarActiveTintColor: '#2563eb',
        tabBarInactiveTintColor: '#6b7280',
        headerTitleAlign: 'center'
      })}
    >
      <Tab.Screen
        name="Serials"
        component={SerialListScreen}
        options={{ title: 'Seri Numaraları' }}
      />
      <Tab.Screen
        name="Transfer"
        component={TransferScreen}
        options={{ title: 'Sevk İşlemleri' }}
      />
      <Tab.Screen
        name="QRSale"
        component={QRSaleScreen}
        options={{ title: 'QR ile Satış' }}
      />
    </Tab.Navigator>
  )
}

export default MainTabNavigator

