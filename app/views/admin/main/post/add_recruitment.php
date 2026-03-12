<?php
/**
 * add recruitment management view for admin panel
 */
?>

<div class="container">
  <!-- Header -->
  <div class="header">
    <div class="left">
      <span class="back">←</span>
      <h1>New Recruitment</h1>
    </div>

    <div class="right">
      <a class="discard">Discard</a>
      <button class="publish">Publish Now</button>
    </div>
  </div>

  <div class="main">
    <!-- LEFT CONTENT -->
    <div class="content">
      <label>Headline</label>
      <input type="text" placeholder="Enter article title..." />

      <label>Excerpt / Summary</label>
      <textarea
        placeholder="Short summary for social media and listings..."
      ></textarea>

      <label>Content Body</label>

      <div class="editor">
        <div class="toolbar">
          <button>B</button>
          <button>I</button>
          <button>Link</button>
          <button>Image</button>
          <button>H1</button>
          <button>H2</button>
          <button>Quote</button>
        </div>

        <div class="editor-body">Start writing your story...</div>
      </div>
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="sidebar">
      <div class="card">
        <h3>Publishing Settings</h3>

        <label>Category</label>
        <select>
          <option>Politics</option>
          <option>Technology</option>
        </select>

        <label>Status</label>
        <select>
          <option>Draft</option>
          <option>Published</option>
        </select>

        <label>Author</label>
        <input type="text" value="Alex Editor" />

        <button class="draft">Save as Draft</button>
      </div>

      <div class="card">
        <h3>Featured Image</h3>
        <div class="upload">Upload Image</div>
      </div>
    </div>
  </div>
</div>
