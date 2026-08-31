import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { WelcomeApp } from './components/WelcomeApp';
import { UserListSkeletonDemo } from './components/UserListSkeletonDemo';

// Registry of available React components for dynamic mounting in Blade
const componentRegistry: Record<string, React.ComponentType<any>> = {
  WelcomeApp,
  UserListSkeletonDemo,
};

document.addEventListener('DOMContentLoaded', () => {
  // 1. Check for standard #app or #root container
  const defaultContainer = document.getElementById('app') || document.getElementById('root');
  if (defaultContainer) {
    const propsJson = defaultContainer.getAttribute('data-props');
    const props = propsJson ? JSON.parse(propsJson) : {};
    const root = createRoot(defaultContainer);
    root.render(
      <React.StrictMode>
        <WelcomeApp {...props} />
      </React.StrictMode>
    );
  }

  // 2. Dynamic auto-mount for any element with data-react-component attribute
  const dynamicContainers = document.querySelectorAll<HTMLElement>('[data-react-component]');
  dynamicContainers.forEach((container) => {
    const componentName = container.getAttribute('data-react-component');
    if (componentName && componentRegistry[componentName]) {
      const Component = componentRegistry[componentName];
      const propsJson = container.getAttribute('data-props');
      const props = propsJson ? JSON.parse(propsJson) : {};
      const root = createRoot(container);
      root.render(
        <React.StrictMode>
          <Component {...props} />
        </React.StrictMode>
      );
    }
  });
});

export { WelcomeApp, UserListSkeletonDemo };
