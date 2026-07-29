
import { ref } from 'vue'
import debounce from 'lodash/debounce'
import axios from 'axios'
import { route } from 'ziggy-js'

export interface Customer {
    id: number
    username: string
    name: string
    email: string
    accessible_theme_category_ids: number[]
}

export function useCustomerSearch() {
    const customers = ref<Customer[]>([])
    const loading = ref(false)

    const fetchCustomers = async (keyword = ''): Promise<void> => {
        loading.value = true

        try {
            const { data } = await axios.get<Customer[]>(
                route('admin.users.search'),
                {
                    params: {
                        q: keyword,
                    },
                },
            )

            customers.value = data
        } catch (error) {
            console.error(error)
            customers.value = []
        } finally {
            loading.value = false
        }
    }

    const search = debounce((keyword: string) => {
        void fetchCustomers(keyword)
    }, 300)

    return {
        customers,
        loading,
        fetchCustomers,
        search,
    }
}