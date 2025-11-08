import React from 'react'
import { KeyboardAvoidingView, Platform, ScrollView, StyleSheet, View, ViewStyle } from 'react-native'

type Props = {
  children: React.ReactNode
  scrollable?: boolean
  style?: ViewStyle
}

const ScreenContainer: React.FC<Props> = ({ children, scrollable = false, style }) => {
  const content = scrollable ? (
    <ScrollView contentContainerStyle={[styles.content, style]}>{children}</ScrollView>
  ) : (
    <View style={[styles.content, style]}>{children}</View>
  )

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      {content}
    </KeyboardAvoidingView>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc'
  },
  content: {
    padding: 16,
    gap: 16
  }
})

export default ScreenContainer

