import React from 'react'
import { Pressable, StyleSheet, Text, ViewStyle } from 'react-native'

type Props = {
  title: string
  onPress: () => void
  disabled?: boolean
  style?: ViewStyle
}

const PrimaryButton: React.FC<Props> = ({ title, onPress, disabled, style }) => (
  <Pressable
    onPress={onPress}
    disabled={disabled}
    style={({ pressed }) => [
      styles.button,
      style,
      pressed && !disabled ? styles.buttonPressed : undefined,
      disabled ? styles.buttonDisabled : undefined
    ]}
  >
    <Text style={styles.text}>{title}</Text>
  </Pressable>
)

const styles = StyleSheet.create({
  button: {
    backgroundColor: '#2563eb',
    paddingVertical: 14,
    paddingHorizontal: 18,
    borderRadius: 12,
    alignItems: 'center'
  },
  buttonPressed: {
    opacity: 0.85
  },
  buttonDisabled: {
    backgroundColor: '#9ca3af'
  },
  text: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600'
  }
})

export default PrimaryButton

