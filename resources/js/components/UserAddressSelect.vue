

<script>
    export default {
        name: "UserAddressSelect",
        data() {
            return {
                province: '',
                city: '',
                district: ''
            }
        },
        methods: {
            onChangeAddress(value) {

                if (value.length === 3) {
                    this.province = value[0]
                    this.city = value[1]
                    this.district = value[2]
                }
            }
        },
        computed: {
            address(){
                return this.province + this.city + this.district
            }
        }

    }
</script>


