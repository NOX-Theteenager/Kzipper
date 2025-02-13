<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Favicon -->
  <link rel="icon" href="Images/Logo_zipper.png">
  <title>Keyce Zipper</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Stylesheet -->
  <link href="css/style.css" rel="stylesheet">
  
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="header-logo">
      <img src="Images/Logo_zipper.png" alt="Logo">
      <p class="header-title">Keyce Zipper</p>
    </div>
    <a href="index.php" class="btn header-btn">Accueil</a>
  </header>

  <main class="container mt-4">
    <div class="main-container">
      <!-- Section image -->
      <div class="image-container">
        <!-- <img src="Images/Image3.png" alt="Image illustrative"> -->
        <iframe src='https://my.spline.design/robottutorialinteractiveeventscopy-79e3ad7fcf64decf8cdd6a9f29a61e69/' frameborder='0' width='100%' height='100%'></iframe>
        <!-- <p class="image-text">Compressez vos fichiers rapidement et en toute sécurité avec Keyce Zipper.</p> -->
      </div>
      

      <!-- Section drag-and-drop -->
      <div class="upload-card">
        <center>
          <!-- <img src="Images/Logo_zipper.png" alt="Logo"> -->
          <iframe src='https://my.spline.design/untitled-a6026421c3c455344c481b5b9ed709e0/' frameborder='0' width='100%' height='100%'></iframe>
          <label class="upload-zone" id="dropZone">
  <p>Déposez vos fichiers ici ou cliquez pour choisir un fichier.</p>
  <input type="file" id="fileInput" name="files[]" multiple webkitdirectory>
  </label>
          <div class="text-center mt-4">
              <form id="compressionForm" method="POST" enctype="multipart/form-data" action="compress.php">
                <input type="file" id="fileInput" name="files[]" multiple style="display: none;">
                <button type="button" class="btn btn-primary btn-block mx-2" onclick="compressFiles()">Compresser</button>
                <button type="button" class="btn btn-secondary btn-block mx-2" onclick="decompressFiles()">Décompresser</button>
              </form>
          </div>
          <div class="mt-3 text-center">
            <small class="text-muted">Formats pris en charge : .txt, .zip, .keyce, etc.</small>
          </div>
          <div id="progressContainer" class="progress-container mt-4" style="display: none;">
            <div class="progress">
              <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p id="progressText" class="mt-2 text-center text-muted">Compression en cours...</p>
         </div>

        </center>
        <div class="btn-clear-container">
          <button class="btn-clear-cache" onclick="clearCache()">
            <img src="Images/clear-icon.png" alt="Clear Cache" class="clear-icon">
          </button>
          <span class="tooltip-text">Vider le cache</span>
        </div>
      </div>

      <!-- Section de fichier et animation côte à côte -->
      <div class="file-and-animation">
        <div id="fileContainer" class="file-container">
          <h4>Fichiers sélectionnés :</h4>
          <ul id="fileList" class="file-list"></ul>
        </div>
        <img src="Images/CompressGif.gif" alt="Animation" class="animation">
      </div>
    </div>
  </main>
      <script>
      const fileInput = document.getElementById('fileInput');
      const fileList = document.getElementById('fileList');
      const fileContainer = document.getElementById('fileContainer');
      const dropZone = document.getElementById('dropZone');

      // Empêcher le comportement par défaut pour dragover et drop
      dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('drag-over'); // Optionnel : ajouter une classe pour un effet visuel
      });

      dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('drag-over'); // Optionnel : supprimer l'effet visuel
      });

      // Gérer l'événement de dépôt de fichier
      dropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropZone.classList.remove('drag-over'); // Supprimer l'effet visuel
        const files = Array.from(event.dataTransfer.items)
          .filter(item => item.kind === 'file')
          .map(item => item.getAsFile());

        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file)); // Ajouter les fichiers au dataTransfer
        fileInput.files = dataTransfer.files; // Synchroniser avec fileInput

        handleFiles(files); // Mettre à jour l'affichage
      });


      // Gérer l'événement de sélection de fichier
      fileInput.addEventListener('change', () => {
        const files = Array.from(fileInput.files);
        handleFiles(files);
      });

      // Fonction pour afficher les fichiers
      function handleFiles(files) {
        const animation = document.querySelector('.animation'); // Sélectionnez l'image GIF
        fileList.innerHTML = ''; // Réinitialiser la liste

        if (files.length > 0) {
          fileContainer.style.display = 'block'; // Afficher le conteneur
          animation.style.display = 'block'; // Afficher l'animation

          files.forEach((file, index) => {
            const relativePath = file.webkitRelativePath || file.name;
            const li = document.createElement('li');
            li.innerHTML = `
              <span>${relativePath} - ${(file.size / 1024).toFixed(2)} KB</span>
              <button class="btn btn-sm btn-danger" onclick="removeFile(${index})">Supprimer</button>
            `;
            fileList.appendChild(li);
          });
        } else {
          fileContainer.style.display = 'none'; // Masquer le conteneur
          animation.style.display = 'none'; // Masquer l'animation
        }
      }


      //Fonction pour supprimer un fichier de la liste
      function removeFile(index) {
        const animation = document.querySelector('.animation');
        const files = Array.from(fileInput.files);
        files.splice(index, 1); // Supprimer le fichier de la liste
        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
        fileInput.dispatchEvent(new Event('change')); // Rafraîchir la liste

        // Masquer l'animation si aucun fichier n'est présent
        if (fileList.children.length === 0) {
          fileContainer.style.display = 'none';
          animation.style.display = 'none';
        }
      }

      // Fonction pour mettre à jour la barre de progression
function updateProgress(percent, message) {
  const progressBar = document.getElementById('progressBar');
  const progressText = document.getElementById('progressText');
  const progressContainer = document.getElementById('progressContainer');

  progressBar.style.width = percent + '%';
  progressBar.innerText = percent + '%';
  progressText.innerText = message;

  if (percent === 100) {
    setTimeout(() => {
      progressContainer.style.display = 'none'; // Masquer après un court délai
    }, 500);
  }
}

// Fonction pour compresser les fichiers avec progress bar
async function compressFiles() {
  const formData = new FormData();
  Array.from(fileInput.files).forEach(file => {
          const relativePath = file.webkitRelativePath || file.name;
          formData.append('files[]', file);
          formData.append('paths[]', relativePath); // Inclure le chemin relatif
        });


  const progressContainer = document.getElementById('progressContainer');
  progressContainer.style.display = 'block';
  updateProgress(0, 'Préparation des fichiers...');

  try {
    const response = await fetch('compress.php', {
      method: 'POST',
      body: formData,
    });

    updateProgress(50, 'Compression en cours...');
    const result = await response.json();

    if (result.success) {
      updateProgress(100, 'Compression terminée avec succès !');
      fileList.innerHTML = ''; // Réinitialiser la liste

      const li = document.createElement('li');
            li.innerHTML = `
              <span>${result.data.name}</span>
              <a href="${result.data.path}" download class="btn btn-sm  btn-block mx-2 btn-success">Télécharger</a>
            `;

      fileList.appendChild(li);
      fileContainer.style.display = 'block';
    } else {
      alert(`Erreur serveur : ${result.message}`);
      updateProgress(100, 'Erreur lors de la compression.');
    }
  } catch (error) {
    alert(`Erreur : ${error.message}`);
    console.error(`Erreur: ${error.message}`)
    updateProgress(100, 'Erreur critique.');
  }
}

// Fonction pour décompresser les fichiers avec progress bar
async function decompressFiles() {
  const formData = new FormData();
  Array.from(fileInput.files).forEach(file => {
      formData.append('files[]', file);
    });


  const progressContainer = document.getElementById('progressContainer');
  progressContainer.style.display = 'block';
  updateProgress(0, 'Préparation des fichiers...');

  try {
    const response = await fetch('decompress.php', {
      method: 'POST',
      body: formData,
    });

    updateProgress(50, 'Décompression en cours...');
    const result = await response.json();

    if (result.success) {
      updateProgress(100, 'Décompression terminée avec succès !');
      fileList.innerHTML = '';

      const li = document.createElement('li');
      li.innerHTML = `
        <span>${result.data.name}</span>
        <a href="${result.data.path}" download class="btn btn-sm btn-success">Télécharger ZIP</a>
      `;
      fileList.appendChild(li);
      fileContainer.style.display = 'block';
    } else {
      alert(`Erreur serveur : ${result.message}`);
      updateProgress(100, 'Erreur lors de la décompression.');
    }
  } catch (error) {
    alert(`Erreur : ${error.message}`);
    updateProgress(100, 'Erreur critique.');
  }
}

// Fonction pour supprimer un fichier généré de la liste
function removeGeneratedFile(index) {
  const fileElement = document.querySelector(`[data-index="${index}"]`);
  if (fileElement) {
    fileElement.remove(); // Supprime l'élément de la liste
  }

  // Masquer le conteneur si la liste est vide
  if (fileList.children.length === 0) {
    fileContainer.style.display = 'none';
  }

// Supprimer le fichier côté serveur
fetch('delete_file.php', {
    method: 'POST',
    body: JSON.stringify({ path: filePath }),
    headers: { 'Content-Type': 'application/json' },
  });

}

      //vider le cache de uploads et output
      async function clearCache() {
        try {
          const response = await fetch('clear_cache.php', { method: 'POST' });
          const result = await response.json();

          if (result.success) {
            alert('Le cache a été vidé avec succès.');
            location.reload(); // Actualise la page
          } else {
            alert(`Erreur lors du vidage du cache : ${result.message}`);
          }
        } catch (error) {
          alert(`Erreur : ${error.message}`);
        }
      }

      


    </script>
  </body>
</html>