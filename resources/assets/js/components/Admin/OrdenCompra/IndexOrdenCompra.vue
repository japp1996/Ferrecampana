<template id="template-orden-compra-index">
	<div>
	    <div class="container">
	    	<div id="myModal" :style="modal ? 'display:block' : 'display:none'" class="modal_ferre">
			  <!-- Modal content -->
					<div class="modal-content">
						<div class="modal-header">
						<span class="close" v-on:click="modal = false">&times;</span>
							<h3>Detalles</h3>
					</div>
					<div class="modal-body" id="modal-body">
							<select v-if="proveedores" name="proveedor_id" v-model="form.proveedor_id" id="proveedor_id">
								<option value="">Seleccione proveedor</option>
								<option v-for="(prov, i) in proveedor" :key="i" :value="prov.id">{{prov.name}}</option>
							</select>
							<button v-if="proveedores" class="btn btn-primary" @click="_do()">Importar</button>
					</div>
					</div>
				</div>
	       <div class="row">
            <div class="col-sm-12 center-align">
                <h1 v-if="options == 0">Orden de Compra</h1>
                <h1 v-if="options == 1 || options == 2">{{ options == 1 ? 'Importar' : 'Actualizar'}} Requisición</h1>
            </div>
	      </div>
	      <div class="row">
	      	<div class="col-sm-12 container-btn-add" >
                <button v-if="options == 0" class="btn btn-success" @click="_add()"> Importar Requisición </button>
                <button v-if="options == 1 || options == 2" class="btn btn-success" @click="_back()">Volver</button>
          </div>
	        <div class="col-md-12">
	         <div class="datagrid" v-if="options == 0 || options == 1">
						<table-byte :set-table="dataTable" :filters="['usuario.name', 'proveedor.contact_name']">
									<table-row slot="table-head">
											<table-head v-if="options == 0">ORDEN.</table-head>
											<table-head v-if="options == 1 || options == 2">REQ.</table-head>
											<table-head>Cliente</table-head>
											<table-head v-if="options == 0">Proveedor</table-head>
											<table-head>Fecha</table-head>
											<table-head>Acciones</table-head>
									</table-row>

									<table-row slot="table-row" slot-scope="{ item }">
											<table-cell>{{ item.id }}</table-cell>
											<table-cell>{{ item.usuario.name }}</table-cell>
											<table-cell v-if="options == 0">{{ item.proveedor != null ? item.proveedor.contact_name : 'SIN PROVEEDOR' }}</table-cell>
											<table-cell>{{ item.created_at }}</table-cell>
											<table-cell>
														<button v-if="options == 1" class="btn btn-success" @click="_import(item)">
										Importar
									</button>
												<a @click="_see(item)" v-if="options == 1" :id="'myBtn'+item.id">
															<img src="http://localhost:8080/ferrecampana/public/images/icons/see.png" class="img-responsive" width="30px" style="display:inline-block">
														</a>
												<button v-if="options != 1" class="btn btn-danger" @click="_delete(item.id)">
										Borrar
									</button>
											</table-cell>
									</table-row>

									<table-row slot="empty-rows">
											<table-cell colspan="4">
													No se encontraron registros.
											</table-cell>
									</table-row>
							</table-byte>
			 			</div>
					</div>
		 		</div>
		</div>
	</div>
</template>
<style>
		.modal_ferre {
	    display: none; /* Hidden by default */
	    position: fixed; /* Stay in place */
	    z-index: 1; /* Sit on top */
	    left: 0;
	    top: 0;
	    width: 100%; /* Full width */
	    height: 100%; /* Full height */
	    overflow: auto; /* Enable scroll if needed */
	    background-color: rgb(0,0,0); /* Fallback color */
	    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}

	/* Modal Content/Box */
	.modal-content {
	    background-color: #fefefe;
	    margin: 15% auto; /* 15% from the top and centered */
	    padding: 20px;
	    border: 1px solid #888;
	    width: 80%; /* Could be more or less, depending on screen size */
	}

	/* The Close Button */
	.close {
	    color: #aaa;
	    float: right;
	    font-size: 28px;
	    font-weight: bold;
	}

	.close:hover,
	.close:focus {
	    color: black;
	    text-decoration: none;
	    cursor: pointer;
	}
</style>
<script>
	export default {
		template: "#template-orden-compra-index",
		data() {
			return {
				options: 1,
				form: {
					proveedor_id: ""
				},
				proveedor:[],
				dataTable: [],
				filters: [],
				modal: false
				
			}
		},
		methods: {
			_add() {
				this.options = 1
				this.reg = 0
				this.dataTable = this.requisicion
			},
			_see(item) {
				var modal = document.getElementById('myModal');
				// Get the button that opens the modal
				var btn = document.getElementById("myBtn"+item.id);
				// Get the <span> element that closes the modal
				var span = document.getElementsByClassName("close")[0];
				// When the user clicks on the button, open the modal 
				btn.onclick = function() {
				    document.getElementById("modal-body").innerHTML = "";
				    item.detalles.forEach((el) => {
				    	document.getElementById("modal-body").innerHTML += "<p> Producto: " + el.productos.name + " Cantidad: " + el.cantidad_requisicion + " Proveedor: " + el.productos.proveedor.name +"</p>";
				    })
				    modal.style.display = "block";			    
				}
				// When the user clicks on <span> (x), close the modal
				span.onclick = function() {
				    modal.style.display = "none";
				}
				// When the user clicks anywhere outside of the modal, close it
				window.onclick = function(event) {
				    if (event.target == modal) {
				        modal.style.display = "none";
				    }
				}
			},
			_back() {
				this.options = 0
				this.dataTable = this.orden
			},
			_import(item) {
				this.modal = true
				this.form = item
			},
			_do() {
				if (this.form.proveedor_id == "" || this.form.proveedor_id == undefined) {
					swal("Debe seleccionar un proveedor")
					return false
				}

				swal({
				  title: "¿Desea importar ésta requisición?",
				  text: "Ésta acción traerá los datos de la requisición y creará una nueva Orden de compra!",
				  icon: "info",
				  buttons: true,
				  dangerMode: true,
				})
				.then((importar) => {
				  if (importar) {
				    axios.post("intranet/orden", this.form)
				    .then( resp => {
							this.modal = false
							this.form.proveedor_id = ""
							swal(resp.data.text, {
					      icon: "success",
					    });
					    this._reloadTable()
					    this.options = 0
				    })
				    .catch( erro => {
							this.modal = false
							this.form.proveedor_id = ""
				    	swal("Lo sentimos! ha ocurrido un error!", {
					      icon: "error",
					    });
				    })
				  }
				});
			},
			_delete(id) {
				swal({
				  title: "¿Estás seguro?",
				  text: "Una vez borrado, no podrás revertir esta acción!",
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
				})
				.then((willDelete) => {
				  if (willDelete) {
				    axios.delete(`intranet/orden/`+id)
				    .then( resp => {
					    swal(resp.data.text, {
					      icon: "success",
					    });
					    this._reloadTable()
				    })
				    .catch( erro => {
				    	swal("Lo sentimos! ha ocurrido un error!", {
					      icon: "error",
					    });
				    })
				  }
				});
			},
			_reloadTable() {
				axios.get("intranet/orden-getr")
				.then( resp => {
					this.requisicion = resp.data
				    this.dataTable = resp.data
				})
				.catch( erro => {

				})
				.then( res => {
					console.log(res)
				})

				axios.get("intranet/orden-get")
				.then( resp => {
					this.orden = resp.data
				  this.dataTable = resp.data
				})
				.catch( erro => {

				})
				.then( res => {
					console.log(res)
				})

			}
		},
		props: {
			orden: {
				type: Array,
				default: []
			},
			requisicion: {
				type: Array,
				default: []
			},
			proveedores: {
				type: Array,
				default: []
			}
		},
		mounted() {	
			this.proveedor = this.proveedores
			this.dataTable = this.requisicion
		}
	}
</script>