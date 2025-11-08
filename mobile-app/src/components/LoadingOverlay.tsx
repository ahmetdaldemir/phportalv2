import React from 'react'
import { ActivityIndicator, StyleSheet, View, Text } from 'react-native'

type Props = {
  message?: string
}

const LoadingOverlay: React.FC<Props> = ({ message }) => (
  <View style={styles.container}>
    <ActivityIndicator size="large" color="#2563eb" />
    {message ? <Text style={styles.text}>{message}</Text> : null}
  </View>
)

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#f4f6fb'
  },
  text: {
    marginTop: 12,
    color: '#1f2937',
    fontSize: 16,
    fontWeight: '500'
  }
})

export default LoadingOverlay

