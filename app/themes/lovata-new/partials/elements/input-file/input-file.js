export default new class inputFile {

  constructor () {
    this.handler();
    this.sizeMb = 10;
    this.collectionFiles = {};
    this.allowFormatted = [
      "image/png",
      "image/jpeg",
      "text/plain",
      "application/pdf",
      "image/apng",
      "image/pjpeg",
      "image/webp"
    ];
    this.errors = {
      format: "неверный формат",
      size: `размер файла не может превышать ${this.sizeMb}Mb`
    };
  }

  handler () {
    this.eventChangeInput();
  }

  eventChangeInput () {
    const inputsFile = document.querySelectorAll(".js-input-file-input");
    if (!inputsFile) return false;
    inputsFile.forEach(input => input.addEventListener("change", (event) => this.changeFile(event, input)));
    inputsFile.forEach(input => input.addEventListener("dragenter", (e) => this.eventDragenter(e)));
    inputsFile.forEach(input => input.addEventListener("dragleave", (e) => this.eventDragleave(e)));

  }

  eventDragenter (e) {
    this.setHoverDropbox(e.target.closest(".js-file-wrap").querySelector(".js-input-dropbox"), true);
  }

  eventDragleave (e) {
    if (e.target.classList.contains("js-input-file-input")) {
      this.setHoverDropbox(e.target.closest(".js-file-wrap").querySelector(".js-input-dropbox"));
    }
  }

  setHoverDropbox (elem, isActive) {
    if (isActive) {
      if (!elem.classList.contains("active")) elem.classList.add("active");
    } else if (elem.classList.contains("active")) {
      elem.classList.remove("active");
    }
  }

  changeFile (event, element) {
    event.preventDefault();
    const files = element.files;
    this.setError(element);
    for (const file of files) {
      if (this.checkFileValidete(file, element)) return false;
      this.setCollectionFiles(element, file);
    }
    element.files = this.collectionFiles[element.id].files;
    this.setListElements(element);

    if (!element.multiple) {
      this.hideAreaButton(element);
    }
  }

  hideAreaButton (element) {
    element.closest(".js-file-wrap").classList.add("hidden");
  }

  setCollectionFiles (element, file) {
    if (!this.collectionFiles[element.id]) {
      this.collectionFiles[element.id] = new DataTransfer();
    }
    this.collectionFiles[element.id].items.add(file);
  }

  setListElements (element) {
    const allFiles = this.collectionFiles[element.id].files;
    const list = element.closest(".js-input-file").querySelector(".js-input-file-list");
    let li = "";
    for (const file of allFiles) {
      li = li + this.setElementAttachment(element, file);
    }
    list.innerHTML = li;
    this.setEventsClose();
  }

  setElementAttachment (element, file) {
    const data = {
      title: `${file.name} (${this.labelSizeText(file.size)})`,
      name: file.name,
      id: element.id
    };
    let inputTemplate = document.querySelector("#input-file-template");
    return this.interpolate(inputTemplate.innerHTML, data);
  }

  setEventsClose () {
    const buttonsClose = document.querySelectorAll(".js-file-close");
    buttonsClose.forEach(button => button.addEventListener("click", () => this.deleteFile(button)));
  }

  deleteFile (button) {
    this.deleteFileFromList(button);
    this.deleteFileFromInput(button);
    const wrap = document.querySelector(`.js-input-file-input#${button.dataset.id}`).closest(".js-file-wrap");

    if (wrap.classList.contains("hidden")) {
      wrap.classList.remove("hidden");
    }
  }

  deleteFileFromList (button) {
    button.closest(".js-input-file-list").removeChild(button.closest("li"));
  }

  deleteFileFromInput (button) {
    const { id, name } = button.dataset;
    const dataTransfer = this.collectionFiles[id];
    const index = Object.keys(dataTransfer.files).find(key => dataTransfer.files[key].name == name);
    dataTransfer.items.remove(index);
  }

  labelSizeText (size) {
    return size >= 1000000 ? this.getMb(size) : this.getKb(size);
  }

  getKb (size) {
    return (size / 1000).toFixed(1) + "Kb";
  }

  getMb (size) {
    return (size / 1000000).toFixed(1) + "Mb";
  }

  checkFileValidete (file, element) {
    const errors = [];
    const format = this.checkFormat(file);
    const size = this.checkSize(file, element);
    if (format) errors.push(format);
    if (size) errors.push(size);
    return errors.length > 0;
  }

  setError (element, errors) {
    const errorWrap = element.closest(".js-input-file").querySelector(".js-error-text");
    errorWrap.innerText = errors ? errors.join(", ") : "";
    if (errors) {
      errorWrap.classList.remove("hidden");
    } else {
      errorWrap.classList.add("hidden");
    }
  }

  checkFormat (file) {
    return !!this.allowFormatted.find(el => el == file.type) ? false : this.errors.format;
  }

  checkSize (file, element) {
    const dataSizeDefault = element.dataset.size || this.sizeMb;
    return (file.size / 1000000) < dataSizeDefault ? false : this.errors.size;
  }

  interpolate (template, params) {
    const replaceTags = { "&": "&amp;", "<": "&lt;", ">": "&gt;" };
    const safeInnerHTML = text => text.toString().replace(/[&<>\(\)]/g, tag => replaceTags[tag] || tag);
    const keys = Object.keys(params);
    const keyVals = Object.values(params).map(safeInnerHTML);
    return new Function(...keys, `return \`${template}\``)(...keyVals);
  }

};
