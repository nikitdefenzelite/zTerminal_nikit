Cypress.Commands.add('runCypressTest', testName => {
    return cy.task('runCypressTest', { testName });
  });
  