async function resolvePageComponent(name, pages, defaultLayout) {
  const path = Object.keys(pages).sort((a, b) => a.length - b.length).find((path2) => path2.endsWith(`${name.replaceAll(".", "/")}.vue`));
  if (!path) {
    throw new Error(`Page component "${name}" could not be found.`);
  }
  let component = typeof pages[path] === "function" ? await pages[path]() : pages[path];
  component = component.default ?? component;
  component.layout ?? (component.layout = defaultLayout);
  return component;
}

export { resolvePageComponent };
