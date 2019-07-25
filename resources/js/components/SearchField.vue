<template>
    <div v-if="!currentSchema || !currentSchema.search || !currentSchema.search.operators" class="text-danger">
        <span v-if="!currentSchema">Error: No schema found for {{ field }}.</span>
        <span v-else>Invalid: Schema for "{{ field }}" lacks "search" or "search.operators"</span>
    </div>
    <div v-else class="d-flex my-1">
        <div class="flex-grow-0">
            <select class="form-control field-select"
                :name="`f${index}`"
                :value="field"
                @input="$emit('field', $event.target.value)"
            >
                <option v-for="field in schema.fields"
                        :key="field.key"
                        v-if="field.search && (advanced || !field.search.advanced)"
                        :value="field.key"
                >{{ field.label }}</option>

                <optgroup v-for="fieldGroup in schema.groups" :key="fieldGroup.label" :label="fieldGroup.label">
                    <option v-for="field in fieldGroup.fields"
                            v-if="field.search && (advanced || !field.search.advanced)"
                            :value="field.key"
                    >{{ field.label }}</option>
                </optgroup>
            </select>
        </div>

        <div v-if="advanced" class="flex-grow-0 mx-1">
            <select v-if="advanced"
                class="form-control field-select"
                :name="`o${index}`"
                :value="operator"
                @input="$emit('operator', $event.target.value)"
            >
                <option v-for="option in currentOperators"
                        :value="option.value"
                >{{ option.label }}</option>
            </select>
        </div>

        <div class="flex-grow-1 mx-1">
            <component
                v-if="operator != 'isnull' && operator != 'notnull'"
                :is="currentType"
                :name="`v${index}`"
                :value="value"
                :schema="currentSchema"
                @value="$emit('value', $event)"
            ></component>
        </div>

        <div class="flex-grow-0">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    import { get } from 'lodash/object';
    import * as components from './input';

    let fieldMap = null;

    export default {
        name: "search-field",

        components: {
            ...components,
        },

        props: {
            advanced: Boolean,
            index: Number,
            schema: Object,
            field: String,
            operators: Array,
            operator: String,
            value: String,
        },

        computed: {
            currentSchema() {
                if (fieldMap === null) {
                    // Lazy-load field map
                    fieldMap = new Map();
                    this.schema.fields.forEach(field => fieldMap[field.key] = field);
                    this.schema.groups.forEach(fieldGroup => {
                        fieldGroup.fields.forEach(field => fieldMap[field.key] = field);
                    })
                }
                return fieldMap[this.field];
            },
            currentType() {
                return get(this.currentSchema, 'search.type', this.currentSchema.type);
            },
            currentOperators() {
                return this.operators.filter(
                    op => this.currentSchema.search.operators.indexOf(op.value) !== -1
                );
            }
        }
    }
</script>

<style scoped lang="sass">
td
    padding: 2px 6px
</style>