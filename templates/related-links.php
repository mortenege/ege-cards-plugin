<div class="wrap" id="vue-app">
  <h2>Related Links</h2>
  <div style="margin-bottom: 20px"><small>The links are referenced using their Id</small></div>

  <button @click.prevent="addRelatedLink" type="button" class="button button-primary">New</button>

  <table class="wp-list-table widefat striped">
    <thead>
      <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Caption</th>
        <th>Link</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(row, index) in relatedLinks" v-if="editId != row.id" @click="setEditId(row.id)" style="cursor:pointer;">
        <td v-for="key in ['id','title','link_text','link']">{{ row[key] }}</td>
        
        <!--<td>{{ row.id }}</td>
        <td>{{ row.title }}</td>
        <td>{{ row.link_text }}</td>
        <td>{{ row.link }}</td>-->
        
          <!--<input type="number" v-model="row.w1" min="0" max="1" step="0.01" @input="rowUpdated(row)">-->
        
        <td><a href="javascript:void(0)" @click.prevent.stop="removeRelatedLink(row.id)">Delete</a></td>
      </tr>
      <tr v-else>
        <td colspan="5" style="padding:20px 40px">
          <table style="width:100%">
            <tr>
              <td>Title</td>
              <td><input type="text" :value="row.title" class="form-control" @input="updateRow(row, 'title', $event.target.value)"></td>
            </tr>
            <tr>
              <td>Caption</td>
              <td><input type="text" :value="row.link_text" class="form-control" @input="updateRow(row, 'link_text', $event.target.value)"></td>
            </tr>
            <tr>
              <td>Link</td>
              <td><input type="text" :value="row.link" class="form-control" @input="updateRow(row, 'link', $event.target.value)"></td>
            </tr>
            <tr>
              <td></td>
              <td>
                <button @click.prevent="saveRelatedLinks" type="button" class="button button-primary" :class="{disabled: saveButtonDisabled}" :disabled="saveButtonDisabled">Save</button>
                <button @click.prevent="setEditId(null)" class="button button-secondary">Cancel</button>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<style>
.form-control {
  width:100%;
}

.form-control.sm {
  max-width: 40px;
}
</style>

<script>
/*
var _links = [
  {
    'id': '1',
    'title': 'Travel Tip:',
    'link': 'https://simpleflying.com/best-travel-rewards-credit-cards-2019/',
    'link_text': 'Compare The Best Travel Reward Cards So You Can Fly First Class On Miles'
  },
  {
    'id': '2',
    'title': 'Must Read:',
    'link': 'https://simpleflying.com/5-reasons-to-start-with-the-chase-sapphire-preferred-card/',
    'link_text': '5 Reasons To Get Started With The Chase Sapphire Preferred® Card'
  },
  {
    'id': '3',
    'title': 'Travel Better:',
    'link': 'https://simpleflying.com/5-reasons-to-start-with-the-chase-sapphire-preferred-card/',
    'link_text': 'Why You Should Get The Chase Sapphire Preferred® Card'
  },
  {
    'id': '6',
    'title': 'Flying Tip:',
    'link': 'https://simpleflying.com/best-travel-rewards-credit-cards-2019/',
    'link_text': 'Get Points Quicker With These Top Travel Rewards Cards'
  }
]
*/
var tt="<?= addslashes(json_encode(get_option('ege_cards_related_links', []))) ?>";
const app = new Vue({
  el: '#vue-app',
  data: {
    relatedLinks: JSON.parse(tt) || [],
    dirty: false,
    updating: false,
    editId: null,
    editRow: null,
  },
  computed: {
    saveButtonDisabled () {
      return !this.dirty || this.updating
    }
  },
  methods: {
    saveRelatedLinks () {
      // Replace with editing data
      if (this.editRow !== null) {
        let index = null
        for (let i in this.relatedLinks) {
          let o = this.relatedLinks[i]
          if (o.id == this.editRow.id) {
            index = i
            break
          }
        }

        if (index !== null) {
          this.$set(this.relatedLinks, index, {
            id: this.editRow.id,
            title: this.editRow.title,
            link: this.editRow.link,
            link_text: this.editRow.link_text
          })
        }

        this.editRow = null
      }

      let url = '<?= admin_url("admin-ajax.php"); ?>';
      let data = new FormData
      data.append('action', 'ege_cards_save_related_links')
      data.append('links', JSON.stringify(this.relatedLinks))

      this.updating = true
      axios.post(url, data)
      .then((response) => {
        console.log('#ajax', response)
        this.relatedLinks = response.data
        this.updating = false
        this.dirty = false
        this.setEditId(null)
      })
      .catch((error) => {
        alert('Error ' + error.response.status + ': ' + error.response.data.data)
        this.updating = false
      })

    },
    updateRow (row, key, value) {
      this.dirty = true

      if (this.editRow === null) {
        this.editRow = {
          id: row.id,
          title: row.title,
          link: row.link,
          link_text: row.link_text
        }
      }
      this.editRow[key] = value
    },
    setEditId (id) {
      this.editRow = null
      this.editId = id
    },
    removeRelatedLink (id) {
      var del = confirm('Are you sure you want to delete this?')
      if (!del) {
        return
      }

      this.dirty = true

      let index = undefined
      for (let i in this.relatedLinks) {
        if (this.relatedLinks[i].id == id) {
          index = i
          break
        }
      }

      if (index !== undefined) {
        this.relatedLinks.splice(index, 1)
      }

      this.saveRelatedLinks()
    },
    addRelatedLink () {
      this.dirty = true

      let highestId = this.relatedLinks.reduce((maxValue, row) => {
        let m = parseInt(maxValue) || 0
        let n = parseInt(row.id)
        return n > m ? n : m
      }, 0)

      let newId = highestId + 1

      this.relatedLinks.push({
        id: newId,
        title:'',
        link:'',
        link_text:''
      })

      this.setEditId(newId)
    }
  }
})
</script>