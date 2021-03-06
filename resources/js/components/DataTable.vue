<!--
    Important:
    This component is mostly a wrapper for old-school jQuery stuff.
    Please always use it with v-once! And please be careful.
    See https://vuejsdevelopers.com/2017/05/20/vue-js-safely-jquery-plugin/ to learn more.
-->
<template>

    <div v-once>

        <div ref="spinner" style="height:300px; display: flex; justify-content: center; align-items: center">
            <div class="lds-heart"><div></div></div>
        </div>

        <div ref="main" style="opacity: 0">

            <div ref="columnSelectorWrapper"  class="d-flex align-items-center">
                <div class="flex-grow-0 pr-2">
                    {{ __('messages.show_columns') }}
                </div>
                <select multiple
                        ref="columnSelector"
                        class="selectpicker flex-grow-0"
                        data-style=""
                        data-style-base="form-control form-control-sm"
                >
                    <option
                            v-for="field in fields"
                            :key="field.key"
                            :value="field.key">{{ field.label }}</option>

                    <optgroup v-for="group in groups" :key="group.label" :label="group.label">

                        <option
                                v-for="field in group.fields"
                                :key="field.key"
                                :value="field.key">{{ field.label }}</option>

                    </optgroup>
                </select>
            </div>

            <table ref="theTable" class="table hover" style="width:100%">
                <thead>
                    <tr v-if="groups.length">
                        <th v-for="field in fields" :key="field.key"></th>
                        <th class="left-group-divider" v-for="group in groups" :key="group.label" :colspan="group.fields.length">{{ group.label }}</th>
                    </tr>
                    <tr class="tooltipster">
                        <th v-for="col in columns" :key="col.data">
                            {{ col.columnLabel }}
                        </th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

    </div>
</template>

<script>
import { get } from 'lodash/object'
import { difference } from 'lodash/array'
import Cookie from 'js-cookie'

let lastResponse = {};

export default {
  name: 'data-table',
  props: {
    schema: {
      type: Object,
    },
    defaultColumns: {
      type: Array,
    },
    defaultOrder: {
      type: Array,
    },
    initialOrder: {
      type: Array,
    },
    baseUrl: {
      type: String,
    },
    prefix: {
      type: String,
    },
    query: {
      type: Object,
    },
  },
  computed: {

    fields () {
      return this.schema.fields.filter(field => field.showInTableView !== false)
    },

    groups () {
      return this.schema.groups.map(group => (
        {
          label: group.label,
          fields: group.fields.filter(field => field.showInTableView !== false),
        }
      )).filter(group => group.fields.length > 0)
    },

    columns () {
      const visibleColumns = this.getVisibleColumns()

      const columns = []

      const processField = (field) => {
        if (field.showInTableView === false) {
          return
        }
        const col = {
          data: field.key,
          columnLabel: field.label,
          orderable: field.orderable,
          visible: visibleColumns.indexOf(field.key) !== -1,
          render: this.renderCell,
        }
        if (get(field, 'columnClassName')) {
          col.className = field.columnClassName
        }
        columns.push(col)
      }

      this.schema.fields.forEach(field => processField(field))
      this.schema.groups.forEach(fieldGroup => {
        fieldGroup.fields.forEach(field => processField(field))
      })
      return columns
    },
  },

  methods: {

    getOrder() {
      const visibleColumns = this.getVisibleColumns()

      const keys = this.columns.map(col => col.data)
      let order = this.initialOrder
      order = order.filter(item => visibleColumns.indexOf(item.key) !== -1)
      return order.map(item => [keys.indexOf(item.key), item.direction])
    },

    getLink(recordId) {
      let url = `${this.baseUrl}/record/${recordId}`
      if (window.location.search) {
        url += window.location.search
      }
      return url
    },

    getVisibleColumns () {
      return this.getSessionValue('columns', this.defaultColumns)
    },

    updateGroupDividers (table, visibleColumns) {
      visibleColumns = visibleColumns || this.getVisibleColumns()

      let fieldGroupMap = {}
      this.schema.fields.forEach(field => fieldGroupMap[field.key] = null)
      this.schema.groups.forEach(fieldGroup => {
        fieldGroup.fields.forEach(field => fieldGroupMap[field.key] = fieldGroup.label)
      })


      const keys = this.columns.map(col => col.data)

      let groupsSeen = []
      let groupBoundaries = []
      visibleColumns.forEach(colKey => {
        let colGroup = fieldGroupMap[colKey]
        if (groupsSeen.indexOf(colGroup) === -1) {
          groupBoundaries.push(keys.indexOf(colKey))
          groupsSeen.push(colGroup)
        }
      })

      table.cells().nodes().flatten().to$().removeClass('left-group-divider')
      table.header().to$().removeClass('left-group-divider')
      table.columns(groupBoundaries).nodes().flatten().to$().addClass('left-group-divider')
      table.columns(groupBoundaries).header().to$().addClass('left-group-divider')
    },

    renderCell (data, type, row, meta) {
      if (data === null) {
        return '–'
      }
      return data
    },

    getSessionValue (name, defaultValue) {
      const key = `ub-baser-${this.prefix}-${name}`

      let value = Cookie.get(key)

      if (value === undefined) {
        return defaultValue
      }

      return JSON.parse(value)
    },

    storeSessionValue (name, value) {
      const key = `ub-baser-${this.prefix}-${name}`
      Cookie.set(key, JSON.stringify(value))
    },

    initColumnSelector () {
      return $(this.$refs.columnSelector).val(this.getVisibleColumns())
    },

    initTable () {
      let mouseDownPos = null;

      $(document).ready(() => {
          $('.tooltipster th').tooltipster({
            maxWidth: 250,
            delay: 800,
            theme: 'tooltipster-borderless',
            content: 'Loading...',
            // 'instance' is basically the tooltip. More details in the "Object-oriented Tooltipster" section.
            functionBefore: (instance, helper) => {
              var $origin = $(helper.origin)
              var order = table.order()
              var label = $origin.text()
              var currentIdx = table.column($origin[0]).index()
              label = label.charAt(0).toLowerCase() + label.substring(1)
              if (order.length && order[0][0] == currentIdx) {
                if (order[0][1] === 'asc') {
                  instance.content(this.__('messages.asc_sort_help'))
                } else {
                  instance.content(this.__('messages.desc_sort_help'))
                }
              } else {
               instance.content(this.__('messages.sort_by_this_column'))
              }
            }
          })
      })

      const table = $(this.$refs.theTable).DataTable({

        fixedHeader: true,

        // Define which table control elements should appear and in what order.
        dom: '<"top"ilpB<"clear">>r<"table-responsive"t><"bottom"ilp<"clear">>',
        buttons: [
          {
            text: '<em class="fa fa-arrows-alt"></em>',
            titleAttr: this.__('messages.toggle_fullscreen'),
            className: 'btn btn-outline-primary dataTablesFullscreenBtn',
            action: ( e, dt, node, config ) => {
              this.toggleFullScreen(table)
            }
          }
        ],

        language: {
          sEmptyTable: this.__('messages.no_records_found'),
          sInfo: '<span class="datatables-info-message">...</span>',
          sInfoEmpty: this.__('messages.no_records_found'),
          sInfoFiltered: '(filtrert fra _MAX_ totalt antall poster)',
          sInfoPostFix: '',
          sInfoThousands: ' ',
          sLengthMenu: this.__('messages.records_per_page_setting'),
          sLoadingRecords: this.__('messages.loading'),
          sProcessing: '<div class="spinner"></div>',
          sSearch: this.__('messages.search'),
          sUrl: '',
          sZeroRecords: this.__('messages.nohits'),
          oPaginate: {
            sFirst: this.__('messages.first'),
            sPrevious: this.__('messages.previous'),
            sNext: this.__('messages.next'),
            sLast: this.__('messages.last'),
          },
          oAria: {
            sSortAscending: ': aktiver for å sortere kolonnen stigende',
            sSortDescending: ': aktiver for å sortere kolonnen synkende',
          },
        },
        initComplete: () => {
          table.table().node().parentElement.addEventListener(
            'scroll',
            this.onTableScroll,
            { passive: true }
          )
        },
        drawCallback: (settings) => {
          let info = table.page.info();
          if (lastResponse) {
            let msg = 'messages.records_shown_of_many',
              msgArgs = {
                start: info.start + 1,
                end: info.end,
              }
            if (!lastResponse.unknownCount) {
              msg = 'messages.records_shown_of_total'
              msgArgs.total = lastResponse.recordsTotal
            }
            $('.datatables-info-message').text(this.__(msg, msgArgs))
          }
          this.updateGroupDividers(table)
        },
        pageLength: this.getSessionValue('page-length', 50),
        lengthMenu: [10, 50, 100, 500, 1000],
        ajax: (input, cb) => {
          const data = {
            ...this.query,
            draw: input.draw,
            start: input.start,
            length: input.length,
            order: input.order,
            columns: input.columns.map(column => column.data),
          }
          $.getJSON(`${this.baseUrl}/data`, data, function(res) {
            cb(res)
          })
        },
        columns: this.columns,
        order: this.getOrder(),

        searching: false,
        processing: true,
        serverSide: true,
        // Make the tr elements focusable
        createdRow: (row, data, dataIndex) => {
          $(row)
            .attr('tabindex', '0')
            .on('mousedown', $event => { mouseDownPos = [$event.clientX, $event.clientY] })
            .on('keypress', $event => {
              if ($event.keyCode === 13) {
                window.location = this.getLink(data[this.schema.primaryId])
              }
            })
            .on('click', $event => {
              const link = this.getLink(data[this.schema.primaryId])
              if (mouseDownPos !== null) {
                const drag = [
                  Math.sqrt(($event.clientX - mouseDownPos[0])**2),
                  Math.sqrt(($event.clientY - mouseDownPos[1])**2),
                ]
                if (drag[0] > 10 || drag[1] > 10) {
                  // Cancel, this was a drag, not a click
                  return
                }
              }
              mouseDownPos = null
              if ($event.ctrlKey || $event.metaKey) {
                window.open(link, '_blank')
              } else {
                window.location = link
              }
            })
        },
      })

      return table
    },

    onTableScroll (ev) {
      // Update the horizontal scroll position of the floating header
      $('.fixedHeader-floating').scrollLeft(ev.target.scrollLeft)
    },

    toggleFullScreen (table) {
      const container = table.table().container()
      $(container).toggleClass('dataTablesFullscreen')
      // Notify the fixedHeader plugin
      $(window).trigger('datatable:togglefullscreen')
    },
  },

  mounted () {
    // Initialize the column selector and the table
    const $columnSelector = this.initColumnSelector()
    const table = this.initTable()

    // Connect them together: Update the table when the column selector change.
    $columnSelector.on('change', () => {
      const currentlyVisible = this.getVisibleColumns()

      const visible = $columnSelector.val() // array of keys
      this.storeSessionValue('columns', visible)

      const keys = this.columns.map(col => col.data)

      const toHide = difference(currentlyVisible, visible)
      const toShow = difference(visible, currentlyVisible)

      const toHideIdx = toHide.map(x => keys.indexOf(x))
      const toShowIdx = toShow.map(x => keys.indexOf(x))

      table.columns(toShowIdx).visible(true, false)
      table.columns(toHideIdx).visible(false, false)

      this.updateGroupDividers(table, visible)
    })

    // Tweak the table header and move the column selector into it
    table.on('init', (event) => {
      // Let's first get a reference to the row above the table. This row contains
      // two cells, the first one containing "Vis X poster", the second one being empty
      // (it's used for the search box that we have disabled above).
      // const $tableWrapper = $(table.containers()[0])
      // const $tableHeader = $tableWrapper.find('> .row:first')

      // // Swap the columns, so that the empty cell comes first.
      // // We will use this for our column selector.
      // const $container = $tableHeader.find('> div:last').detach()
      // $tableHeader.prepend($container)

      // // Then move our column selector into it
      // $container.append($(this.$refs.columnSelectorWrapper).detach())

      // // Right-align the "Vis X poster" cell (which is now the last cell)
      // $tableHeader.find('> div:last').addClass('text-right')

      // We're ready!
      $(this.$refs.spinner).hide()
      $(this.$refs.main).css('opacity', '1')
    })

    table.on('length', (e, settings, len) => {
      this.storeSessionValue('page-length', len)
    })

    table.on('order', () => {
      const defaultOrder = this.defaultOrder.map(item => item.key + ':' + item.direction).join(',')
      const order = table.order().map(item => (this.columns[item[0]].data +':' + item[1])).join(',')
      if (order === defaultOrder) {
        this.$emit('order', {order: ''})
      } else {
        this.$emit('order', {order})
      }
    })

    table.on('xhr', ( e, settings, json, xhr ) => {
      lastResponse = json;
    })
  },
}

</script>
