import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const BookForm = ({ book, onSave, onCancel }) => {
    const isEdit = !!book;
    const [formData, setFormData] = useState({
        title: book?.title || '',
        author: book?.author || '',
        description: book?.description || '',
        publication_year: book?.publication_year || '',
        status: book?.status || 'available'
    });
    const [isSaving, setIsSaving] = useState(false);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setIsSaving(true);

        const path = isEdit ? `/library/v1/books/${book.id}` : '/library/v1/books';
        const method = isEdit ? 'PUT' : 'POST';

        apiFetch({
            path: path,
            method: method,
            data: formData
        })
        .then(() => {
            setIsSaving(false);
            onSave();
        })
        .catch(error => {
            console.error(error);
            setIsSaving(false);
            alert('Error saving book.');
        });
    };

    return (
        <div className="form-container">
            <h3>{isEdit ? 'Edit Book' : 'Add New Book'}</h3>
            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value={formData.title} onChange={handleChange} required />
                </div>
                <div className="form-group">
                    <label>Author</label>
                    <input type="text" name="author" value={formData.author} onChange={handleChange} />
                </div>
                <div className="form-group">
                    <label>Descriptionn</label>
                    <textarea name="description" value={formData.description} onChange={handleChange} />
                </div>
                <div className="form-group">
                    <label>Publication Year</label>
                    <input type="number" name="publication_year" value={formData.publication_year} onChange={handleChange} />
                </div>
                <div className="form-group">
                    <label>Status</label>
                    <select name="status" value={formData.status} onChange={handleChange}>
                        <option value="available">Available</option>
                        <option value="borrowed">Borrowedd</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
                <button type="submit" className="button button-primary" disabled={isSaving}>
                    {isSaving ? 'Saving...' : 'Save Book'}
                </button>
                <button type="button" className="button" onClick={onCancel} style={{marginLeft: '10px'}}>Cancel</button>
            </form>
        </div>
    );
};

export default BookForm;