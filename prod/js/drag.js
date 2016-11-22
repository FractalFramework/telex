(function(){
	var dndHandler={
		//Propri�t� pointant vers l'�l�ment en cours de d�placement
		draggedElement: null, 
		applyDragEvents: function(element){
			element.draggable=true;
			//Cette variable est n�cessaire pour que l'�v�nement � dragstart � ci-dessous acc�de facilement au namespace � dndHandler �
			var dndHandler=this; 
			element.addEventListener('dragstart', function(e){
				//On sauvegarde l'�l�ment en cours de d�placement
				dndHandler.draggedElement=e.target; 
				//N�cessaire pour Firefox
				e.dataTransfer.setData('text/plain', ''); 
			});
		},
		applyDropEvents: function(dropper){
			dropper.addEventListener('dragover', function(e){
				//On autorise le drop d'�l�ments
				e.preventDefault(); 
				//Et on applique le style ad�quat � notre zone de drop quand un �l�ment la survole
				this.className='dropper drop_hover'; 
			});
			dropper.addEventListener('dragleave', function(){
				//On revient au style de base lorsque l'�l�ment quitte la zone de drop
				this.className='dropper'; 
			});
			//Cette variable est n�cessaire pour que l'�v�nement � drop � ci-dessous acc�de facilement au namespace � dndHandler �
			var dndHandler=this; 
			dropper.addEventListener('drop', function(e){
				var target=e.target,
					//R�cup�ration de l'�l�ment concern�
					draggedElement=dndHandler.draggedElement, 
					//On cr�� imm�diatement le clone de cet �l�ment
					clonedElement=draggedElement.cloneNode(true); 
				//Cette boucle permet de remonter jusqu'� la zone de drop parente
				while (target.className.indexOf('dropper') == -1){
					target=target.parentNode;
				}
				//Application du style par d�faut
				target.className='dropper';
				//Ajout de l'�l�ment clon� � la zone de drop actuelle
				clonedElement=target.appendChild(clonedElement);
				//Nouvelle application des �v�nements qui ont �t� perdus lors du cloneNode()
				dndHandler.applyDragEvents(clonedElement);
				//Suppression de l'�l�ment d'origine
				draggedElement.parentNode.removeChild(draggedElement);
			});
		}
	};
	var elements=document.querySelectorAll('.draggable'),
		elementsLen=elements.length;
	for (var i=0; i < elementsLen; i++){
		//Application des param�tres n�cessaires aux �l�ments d�pla�ables
		dndHandler.applyDragEvents(elements[i]);
	}
	var droppers=document.querySelectorAll('.dropper'),
		droppersLen=droppers.length;
	for (var i=0; i < droppersLen; i++){
		//Application des �v�nements n�cessaires aux zones de drop
		dndHandler.applyDropEvents(droppers[i]); 
	}	
})();