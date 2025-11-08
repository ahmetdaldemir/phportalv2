import { useEffect, useState } from 'react'
import { useAuthStore } from '@store/useAuthStore'

const useAuthBootstrap = () => {
  const bootstrap = useAuthStore(state => state.bootstrap)
  const [isBootstrapping, setIsBootstrapping] = useState(true)

  useEffect(() => {
    let isMounted = true
    bootstrap().finally(() => {
      if (isMounted) {
        setIsBootstrapping(false)
      }
    })

    return () => {
      isMounted = false
    }
  }, [bootstrap])

  return { isBootstrapping }
}

export default useAuthBootstrap

