// Load SECTION5
fetch("../../app/views/pages/introduce/section5.php")
  .then((res) => {
    if (!res.ok) {
      throw new Error(`Failed to load section5.php: ${res.status}`);
    }
    return res.text();
  })
  .then((data) => {
    const section5Container = document.getElementById("section5");
    if (!section5Container) {
      console.error("Section 5: Container element #section5 not found in DOM");
      return;
    }
    section5Container.innerHTML = data;
    console.log("Section 5 loaded successfully");
  })
  .catch((error) => {
    console.error("Error loading section5.php:", error);
  });
