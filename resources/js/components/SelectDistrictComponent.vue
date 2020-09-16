<template>
  <div class="form-group row">
    <label class="col-form-label col-sm-3 text-sm-right">省市区&nbsp;</label>
    <div class="col-sm-3">
      <select class="form-control" v-model="provinceId">
        <option value="">选择省</option>
        <option v-for="(name,id) in provinces" :value="id">{{ name }}</option>
      </select>
    </div>
    <div class="col-sm-3">
      <select class="form-control" v-model="cityId">
        <option value="">选择市</option>
        <option v-for="(name, id) in cities" :value="id">{{ name }}</option>
      </select>
    </div>
    <div class="col-sm-3">
      <select class="form-control" v-model="districtId">
        <option value="">选择区</option>
        <option v-for="(name, id) in districts" :value="id">{{ name }}</option>
      </select>
    </div>
  </div>
</template>
<script>
  import addressData from 'china-area-data'
  import _ from 'lodash'

  export default {
    name: "SelectDistrictComponent",
    props: {
      //接收初始化省市区的值，默认为空，编辑的时候会用到
      //格式 数组 [省,市,区]三个string值
      initValue: {
        type: Array,
        default: () => ([]),//默认是空数组
      }
    },
    data() {
      return {
        provinces: addressData['86'],//省列表
        cities: {},//城市列表
        districts: {},//区列表
        provinceId: '',//当前选中的省
        cityId: '',//当前选择的市
        districtId: '',//当前选择的区
      }
    },
    watch: {
      //监听this.provinceId，发生改变时触发
      provinceId(newValue) {
        //如果选择没有新值
        if (!newValue) {
          this.cities = {};
          this.cityId = '';
          return ;
        }
        //将城市列表设为当前选择的省下面的
        this.cities = addressData[newValue];
        //如果当前选择的城市不在当前选择的省下面，将选中城市清空
        if(! this.cities[this.cityId]) {
          this.cityId = '';
        }
      },
        //监听this.cityId，发生改变时
        cityId(newValue) {
          //如果选择没有新值,清空区
          if (! newValue) {
            this.districts = {};
            this.districtId = '';
            return
          }
          //将区列表设为当前选择的城市下面的区
          this.districts = addressData[newValue];
          //如果当前选中的区不在当前选的城市下面，将选中的区清空
          if(! this.districts[this.districtId]) {
            this.districtId = '';
          }
        },
        //监听this.districtId
        districtId(newValue) {
          //触发一个事件 changeAddress 传递数据，数组 [省,市,区]三个值

          this.$emit('changed-address',[this.provinces[this.provinceId], this.cities[this.cityId], this.districts[this.districtId]])
        }
    },
    created() {
        //created后初始化数据
      this.setFormData(this.initValue)
    },
    methods: {
      setFormData(value) {
        //过滤空值
        value = _.filter(value);
        //如果数组长度=0，将选择的省清空（监听了省，市区也会清空）
        if (value.length === 0) {
          this.provinceId = ''

          return
        }
        //从当前省列表找到数组第一个元素同名的项的索引,可以看findkey()
        const provinceId = _.findKey(this.provinces, (o)=> o === value[0])
        //找不到省，清空选择的省值
        if (! provinceId) {
          this.provinceId = ''
          return
        }
        //找到了，将当前省设为对应的id
        this.provinceId = provinceId

        //找数组第二个元素对应的市id
        const cityId = _.findKey(addressData[provinceId], (o)=> o === value[1])
        //找不到市，清空选择的市
          console.log('find city')
        if(! cityId) {
          this.cityId = ''
          return
        }
        //找到市，将市赋给选择市
        this.cityId = cityId

        //找数组第三个元素对应的区id
        const districtId = _.findKey(addressData[cityId], (o)=> o === value[2])
        //找不到市，清空选择的区
        if(! districtId) {
          this.districtId = ''
          return
        }
        //找到市，将市id赋给选择区
        this.districtId = districtId
      }
    }
  }

</script>

