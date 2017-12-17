<template>
    <div>
        <el-form label-position="top" label-width="10px" :model="form">
            <el-form-item label="Limit channel results to:">
                <el-input-number v-model="form.categoriesLimit" :step="1" :min="2" :max="500"></el-input-number>
            </el-form-item>

            <el-form-item label="Filter channels by...">
                <el-select v-model="form.categoriesFilter" placeholder="Filter channels by..." filterable>
                    <el-option v-for="item in filters" :key="item.value" :label="item.description" :value="item.value">
                    </el-option>
                </el-select>
            </el-form-item>

            <div class="form-toggle no-border">
                Display channel avatars:
                <el-switch v-model="form.showCategoryAvatars"></el-switch>
            </div>

            <el-form-item label="Sidebar Color">
                <el-select v-model="form.color" placeholder="Sidebar Color..." filterable>
                    <el-option
                            v-for="item in colors"
                            :key="item"
                            :label="item"
                            :value="item">
                    </el-option>
                </el-select>
            </el-form-item>
    
            <!-- save -->
            <el-form-item v-if="changed">
                <el-button type="success" @click="save" size="medium">
                    Save
                </el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    categoriesFilter: Store.settings.rightSidebar.categoriesFilter,
                    categoriesLimit: Store.settings.rightSidebar.categoriesLimit, 
                    showCategoryAvatars: Store.settings.rightSidebar.showCategoryAvatars, 
                    color: Store.settings.rightSidebar.color, 
                },

                filters: [
                    {value: 'subscribed', description: 'Channels I have subscribed to',},
                    {value: 'moderating', description: 'Channels I am moderating',},
                    {value: 'bookmarked', description: 'Channels I have bookmarked',},
                ], 

                colors: [
                    'Blue', 'Dark Blue', 'Red', 'Dark', 'Gray', 'Green', 'Purple'
                ],
            }
        },
    
        computed: {
            changed() {
                if (
                    Store.settings.rightSidebar.categoriesFilter != this.form.categoriesFilter ||
                    Store.settings.rightSidebar.categoriesLimit != this.form.categoriesLimit || 
                    Store.settings.rightSidebar.showCategoryAvatars != this.form.showCategoryAvatars || 
                    Store.settings.rightSidebar.color != this.form.color 
                ) {
                    return true;
                }
    
                return false;
            },
        },
    
        methods: {
            save() {
                Store.settings.rightSidebar.categoriesFilter = this.form.categoriesFilter;
                Store.settings.rightSidebar.categoriesLimit = this.form.categoriesLimit;
                Store.settings.rightSidebar.showCategoryAvatars = this.form.showCategoryAvatars;
                Store.settings.rightSidebar.color = this.form.color;
            }
        }
    };
</script>
