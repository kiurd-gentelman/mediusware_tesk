<template>
    <section>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Product Name</label>
                            <input type="text" v-model="product_name" placeholder="Product Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Product SKU</label>
                            <input type="text" v-model="product_sku" placeholder="Product Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea v-model="description" id="" cols="30" rows="4" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                    </div>
                    <div class="card-body border">
                        <vue-dropzone ref="myVueDropzone" id="dropzone"
                                      :options="dropzoneOptions"
                                      @vdropzone-complete="afterComplete"
                                      @vdropzone-removed-file="removeFile"
                        ></vue-dropzone>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Variants</h6>
                    </div>
                    <div class="card-body">
                        <div class="row" v-for="(item,index) in product_variant">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Option</label>
                                    <select v-model="item.option" class="form-control">
                                        <option v-for="variant in variants"
                                                :value="variant.id">
                                            {{ variant.title }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label v-if="product_variant.length != 1" @click="product_variant.splice(index,1) ; checkVariant"
                                           class="float-right text-primary"
                                           style="cursor: pointer;">Remove</label>
                                    <label v-else for="">.</label>
                                    <input-tag v-model="item.tags" @input="checkVariant" class="form-control"></input-tag>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" v-if="product_variant.length < variants.length && product_variant.length < 3">
                        <button @click="newVariant" class="btn btn-primary">Add another option</button>
                    </div>

                    <div class="card-header text-uppercase">Old Variant</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>Variant</td>
                                    <td>Price</td>
                                    <td>Stock</td>
                                    <td>Action</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(variant_price , index) in old_variant">
                                    <td>{{ (variant_price.product_variant_one != null)? variantName(variant_price.product_variant_one) + '-' : ''}}
                                        {{ (variant_price.product_variant_two != null)? variantName(variant_price.product_variant_two) + '-' : '' }}
                                        {{ (variant_price.product_variant_three != null)? variantName(variant_price.product_variant_three) : ''}}</td>
                                    <td>
                                        <label type="text" >{{variant_price.price}}</label>
                                    </td>
                                    <td>
                                        <label type="text"  >{{variant_price.stock}}</label>
                                    </td>
                                    <td>
                                        <label type="text"  ><a class="btn btn-sm text-white btn-danger" @click="deleteVariant(index , variant_price.id )">Delete</a></label>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-header text-uppercase">Preview</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>Variant</td>
                                    <td>Price</td>
                                    <td>Stock</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="variant_price in product_variant_prices">
                                    <td>{{ variant_price.title }}</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="variant_price.price">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="variant_price.stock">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button @click="saveProduct" type="submit" class="btn btn-lg btn-primary">Save</button>
        <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
    </section>
</template>

<script>
    import vue2Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    import InputTag from 'vue-input-tag'

    export default {
        components: {
            vueDropzone: vue2Dropzone,
            InputTag
        },
        props: {
            variants: {
                type: Array,
                required: true
            },
            product:{
                type: '',
                required: true
            },
            product_variant_price_props:{
                type: '',
                required: true
            },
            product_variant_props:{
                type: '',
                required: true
            },
            product_image:{
                type: '',
                required: true
            },

        },
        data() {
            return {
                product_name: '',
                product_sku: '',
                description: '',
                images: [],
                product_variant: [
                    {
                        option: this.variants[0].id,
                        tags: []
                    }
                ],
                product_variant_prices: [],
                dropzoneOptions: {
                    url: '/upload-image',
                    thumbnailWidth: 150,
                    maxFilesize: 0.5,
                    addRemoveLinks: true,
                    removeFile:true,
                    uploadMultiple:true,
                    renameFile: function (file) {
                        var dt = new Date();
                        var time = dt.getTime();
                        var path = time + file.name
                        return path;
                    },
                    removedfile: function (file)  {},
                    headers: {'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,},
                },
                old_variant:this.product_variant_price_props

            }
        },
        methods: {
            // it will push a new object into product variant
            newVariant() {
                let all_variants = this.variants.map(el => el.id)
                console.log(all_variants)
                let selected_variants = this.product_variant.map(el => el.option);
                console.log(selected_variants)
                let available_variants = all_variants.filter(entry1 => !selected_variants.some(entry2 => entry1 == entry2))
                console.log(available_variants)

                this.product_variant.push({
                    option: available_variants[0],
                    tags: []
                })
                console.log(this.product_variant)
            },

            // check the variant and render all the combination
            checkVariant() {
                let tags = [];
                this.product_variant_prices = [];
                this.product_variant.filter((item) => {
                    tags.push(item.tags);
                })

                console.log(this.product_variant)

                this.getCombn(tags).forEach(item => {
                    this.product_variant_prices.push({
                        title: item,
                        price: 0,
                        stock: 0
                    })
                })
            },

            // combination algorithm
            getCombn(arr, pre) {
                pre = pre || '';
                if (!arr.length) {
                    return pre;
                }
                let self = this;
                let ans = arr[0].reduce(function (ans, value) {
                    return ans.concat(self.getCombn(arr.slice(1), pre + value + '/'));
                }, []);
                return ans;
            },

            // store product into database
            saveProduct() {
                let product = {
                    title: this.product_name,
                    sku: this.product_sku,
                    description: this.description,
                    product_image: this.images,
                    product_variant: this.product_variant,
                    product_variant_prices: this.product_variant_prices
                }
                axios.post('/product', product).then(response => {
                    console.log(response.data);
                    window.location ='/product';
                }).catch(error => {
                    console.log(error);

                })

                console.log(product);
            },

            variantName(id){
                let v = this.product_variant_props.filter(item => item.id ==id)
                // console.log()
                return v[0]['variant']
            },
            deleteVariant(index,id){
                console.log(index)
                console.log(id)

                axios.get('/variant-delete/'+id).then(response => {
                    console.log(response.data);
                    this.old_variant.splice(index, 1)
                    console.log(this.product_variant_price_props)
                }).catch(error => {
                    console.log(error);

                })
            },



            removeFile(file){
                var name = file.upload.filename;

                var fileRef;

                console.log(name)
                var tempID = this.product_image.filter(item => item.file_path === name)[0].id

                console.log(tempID)
                axios.post('/delete-image', {'name':name , 'id':tempID}).then(response => {
                    var temparray = this.images.filter(item => item != name)
                    this.images = temparray

                }).catch(error => {
                    console.log(error);

                })
                return (fileRef = file.previewElement) != null ?
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
            afterComplete: async function (response) {

                if (response.status == "success") {
                    console.log(response.upload.filename)
                    console.log("upload successful");

                    this.sendSuccess = true;
                    this.images.push(response.upload.filename)
                } else {
                    console.log("upload failed");
                }
            },


        },
        mounted() {
            console.log('Component mounted.')
            console.log(this.product_variant_props)
            this.product_name = this.product.title;
            this.product_sku = this.product.product_sku;
            this.description = this.product.description;

            let locOption = []
            let locTag = []
            let selectedVariant = [
                {
                    selectedOption: '',
                    selectedTags: []
                }
            ]

            for (var i = 0; i < this.product_image.length; i++) {

                let mockFile = {
                    name: '/images/'+this.product_image[i].file_path,
                    size: 123,
                    type:'image/png',
                    upload: {
                        filename: this.product_image[i].file_path,
                    },

                };
                this.images.push(this.product_image[i].file_path)

                this.$refs.myVueDropzone.manuallyAddFile(
                    mockFile,
                    '/images/'+this.product_image[i].file_path,
                );
            }
            // console.log(this.images)

        }
    }
</script>
